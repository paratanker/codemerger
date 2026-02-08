<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use App\Models\MergeLog;
use App\Models\RepoBranch;
use App\Models\RepoBranchMatch;
use App\Jobs\DeployBranchJob;

class MergeController extends Controller
{
    public function dashboard()
    {
        $summary = ['total' => MergeLog::count(), 'recent' => MergeLog::with('user')->orderByDesc('id')->limit(5)->get()];
        return view('dashboard', compact('summary'));
    }

    public function showForm(Request $request)
    {
        $branchesA = [];
        $branchesB = [];
        $cfg = config('services.bitbucket');

        try {
            $forceRefresh = (bool) $request->boolean('refresh');

            // Try DB cache first for A
            $branchesA = RepoBranch::where('workspace', $cfg['workspace_a'])
                ->where('repo', $cfg['repo_a'])
                ->orderBy('branch')
                ->pluck('branch')
                ->all();
            // Try DB cache first for B
            $branchesB = RepoBranch::where('workspace', $cfg['workspace_b'])
                ->where('repo', $cfg['repo_b'])
                ->orderBy('branch')
                ->pluck('branch')
                ->all();

            $needsFetchA = $forceRefresh || empty($branchesA);
            $needsFetchB = $forceRefresh || empty($branchesB);

            if (!($needsFetchA || $needsFetchB)) {
                return view('merge.form', compact('branchesA', 'branchesB'));
            }

            if ($cfg['auth_mode'] === 'app_password') {
                // API mode (Bitbucket REST)
                if ($cfg['user'] && $cfg['app_password']) {
                    $urlA = "https://api.bitbucket.org/2.0/repositories/{$cfg['workspace_a']}/{$cfg['repo_a']}/refs/branches?pagelen=100";
                    $urlB = "https://api.bitbucket.org/2.0/repositories/{$cfg['workspace_b']}/{$cfg['repo_b']}/refs/branches?pagelen=100";

                    $resA = Http::withBasicAuth($cfg['user'], $cfg['app_password'])->get($urlA);
                    $resB = Http::withBasicAuth($cfg['user'], $cfg['app_password'])->get($urlB);

                    if ($needsFetchA && $resA->successful()) {
                        $branchesA = collect($resA->json('values'))->pluck('name')->all();
                    }
                    if ($needsFetchB && $resB->successful()) {
                        $branchesB = collect($resB->json('values'))->pluck('name')->all();
                    }
                }
            } else {
                // SSH mode (Git commands)
                $sshKey = $cfg['ssh_key'];
                if (!file_exists($sshKey)) {
                    echo "SSH key not found at {$sshKey}";
                    throw new \Exception("SSH key not found at {$sshKey}");
                }

                // Use temp folder for cloning
                $tmp = storage_path('app/git_tmp_' . uniqid());
                mkdir($tmp, 0700, true);

                $repoAUrl = "git@bitbucket.org:{$cfg['workspace_a']}/{$cfg['repo_a']}.git";
                $repoBUrl = "git@bitbucket.org:{$cfg['workspace_b']}/{$cfg['repo_b']}.git";

                putenv("GIT_SSH_COMMAND=ssh -i {$sshKey} -o StrictHostKeyChecking=no");

                $git = $this->getGitPath();
                $outA = $needsFetchA ? [] : [];
                $outB = $needsFetchB ? [] : [];
                if ($needsFetchA) exec("cd {$tmp} && \"{$git}\" ls-remote --heads {$repoAUrl}", $outA);
                if ($needsFetchB) exec("cd {$tmp} && \"{$git}\" ls-remote --heads {$repoBUrl}", $outB);

                if ($needsFetchA) {
                    $branchesA = collect($outA)
                        ->map(fn($line) => preg_replace('#^.+refs/heads/#', '', trim($line)))
                        ->filter()
                        ->values()
                        ->all();
                }
                if ($needsFetchB) {
                    $branchesB = collect($outB)
                        ->map(fn($line) => preg_replace('#^.+refs/heads/#', '', trim($line)))
                        ->filter()
                        ->values()
                        ->all();
                }

                exec("rm -rf {$tmp}");
            }

            // Persist fetched branches into DB (idempotent, without deleting existing rows)
            if ($needsFetchA && !empty($branchesA)) {
                $rows = collect($branchesA)->map(fn($br) => [
                    'workspace' => $cfg['workspace_a'],
                    'repo' => $cfg['repo_a'],
                    'branch' => $br,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])->all();
                // upsert ensures existing rows stay; inserts new branches and updates timestamps
                \DB::table('repo_branches')->upsert(
                    $rows,
                    ['workspace', 'repo', 'branch'],
                    ['updated_at']
                );
            }
            if ($needsFetchB && !empty($branchesB)) {
                $rows = collect($branchesB)->map(fn($br) => [
                    'workspace' => $cfg['workspace_b'],
                    'repo' => $cfg['repo_b'],
                    'branch' => $br,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])->all();
                \DB::table('repo_branches')->upsert(
                    $rows,
                    ['workspace', 'repo', 'branch'],
                    ['updated_at']
                );
            }
        } catch (\Exception $e) {
            \Log::error("Branch fetch failed: " . $e->getMessage());
        }

        return view('merge.form', compact('branchesA', 'branchesB'));
    }

    // Admin: list and create matches between repos
    public function matchIndex()
    {
        $cfg = config('services.bitbucket');
        $branchesA = RepoBranch::where('workspace', $cfg['workspace_a'])
            ->where('repo', $cfg['repo_a'])
            ->orderBy('branch')->get();
        $branchesB = RepoBranch::where('workspace', $cfg['workspace_b'])
            ->where('repo', $cfg['repo_b'])
            ->orderBy('branch')->get();
        $pairs = RepoBranchMatch::with(['branchA','branchB'])->orderByDesc('id')->get();
        return view('merge.match', compact('branchesA','branchesB','pairs'));
    }

    public function matchStore(Request $request)
    {
        $request->validate([
            'branch_a' => 'required|string',
            'branch_b' => 'required|string',
        ]);
        $cfg = config('services.bitbucket');
        $a = RepoBranch::where('workspace', $cfg['workspace_a'])->where('repo', $cfg['repo_a'])->where('branch', $request->branch_a)->firstOrFail();
        $b = RepoBranch::where('workspace', $cfg['workspace_b'])->where('repo', $cfg['repo_b'])->where('branch', $request->branch_b)->firstOrFail();
        RepoBranchMatch::updateOrCreate([
            'branch_a_id' => $a->id,
            'branch_b_id' => $b->id,
        ], [
            'created_by' => auth()->id(),
        ]);
        return back()->with('status', 'Match saved');
    }

    public function matchDelete($id)
    {
        RepoBranchMatch::where('id', $id)->delete();
        return back()->with('status', 'Match removed');
    }

    public function merge(Request $req)
    {
        $req->validate([
            'branchA' => 'required',
            'branchB' => 'required',
            'direction' => 'required|in:AtoB,BtoA'
        ]);

        $user = Auth::user();
        if (!$user->active)
            return back()->withErrors(['account' => 'Your account is deactivated']);

        // Determine direction
        if ($req->direction === 'AtoB') {
            $sWorkspace = config('services.bitbucket.workspace_a');
            $sRepo = config('services.bitbucket.repo_a');
            $sBr = $req->branchA;
            $dWorkspace = config('services.bitbucket.workspace_b');
            $dRepo = config('services.bitbucket.repo_b');
            $dBr = $req->branchB;
        } else {
            $sWorkspace = config('services.bitbucket.workspace_b');
            $sRepo = config('services.bitbucket.repo_b');
            $sBr = $req->branchB;
            $dWorkspace = config('services.bitbucket.workspace_a');
            $dRepo = config('services.bitbucket.repo_a');
            $dBr = $req->branchA;
        }

        $log = MergeLog::create([
            'user_id' => $user->id,
            'direction' => $req->direction,
            'source_repo' => $sRepo,
            'source_branch' => $sBr,
            'dest_repo' => $dRepo,
            'dest_branch' => $dBr,
            'merge_status' => 'pending',
            'created_at' => now()
        ]);

        $authMode = config('services.bitbucket.auth_mode', 'ssh');
        $merge_status = 'error';
        $pr_link = null;
        $deploy_status = null;

        try {
            if ($authMode === 'ssh') {
                // ===================================================
                // SSH MERGE MODE — clone/checkout both repos and sync files
                // ===================================================
                $sshKeyPath = config('services.bitbucket.ssh_key');
                $baseDir = base_path('repos');
                if (!file_exists($baseDir)) mkdir($baseDir, 0775, true);
                // Ensure SSH is used for all git commands
                putenv("GIT_SSH_COMMAND=ssh -i \"{$sshKeyPath}\" -o StrictHostKeyChecking=no");

                // Ensure local clones exist and on correct branches with latest code
                $sRepoDir = $this->ensureRepoAndBranch($baseDir, $sWorkspace, $sRepo, $sBr, $sshKeyPath);
                $dRepoDir = $this->ensureRepoAndBranch($baseDir, $dWorkspace, $dRepo, $dBr, $sshKeyPath);

                // Copy files from source to destination (exclude .git)
                $this->syncWorkingTree($sRepoDir, $dRepoDir);

                $git = $this->getGitPath();
                Log::info('Using git path: ' . $git);
                
                // Restore files that must not change BEFORE staging
                $restoreCmds = [
                    "cd {$dRepoDir}",
                    // "{$git} checkout -- .htaccess || true",
                    // "{$git} checkout -- {$dRepoDir}/common/config/timezone.php || true",
                    "\"{$git}\" restore .htaccess common/config/timezone.php"
                ];
                $restoreOutput = shell_exec(implode(' && ', $restoreCmds) . ' 2>&1');
                Log::info('Restore output: ' . $restoreOutput);
                
                // Stage all changes AFTER restore
                $addOutput = shell_exec("cd {$dRepoDir} && \"{$git}\" add -A 2>&1");
                Log::info('Git add output: ' . ($addOutput ?: 'No output (success)'));
                
                // Check what's staged
                $statusOutput = shell_exec("cd {$dRepoDir} && \"{$git}\" status --short 2>&1");
                Log::info('Git status after add: ' . ($statusOutput ?: 'No changes'));

                // Commit with source latest commit message
                $srcMsg = trim(shell_exec("cd {$sRepoDir} && \"{$git}\" log -1 --pretty=%B"));

                if ($srcMsg === '') {
                    $srcMsg = "Sync {$sRepo}:{$sBr} → {$dRepo}:{$dBr}";
                }

                // Preview only: return diff summary
                $nameStatus = shell_exec("cd {$dRepoDir} && \"{$git}\" diff --cached --name-status 2>&1");
                $stat = shell_exec("cd {$dRepoDir} && \"{$git}\" diff --cached --stat 2>&1");
                $output = "Changes to be committed (preview)\n\n" . trim($srcMsg) . "\n\n" . trim($nameStatus) . "\n\n" . trim($stat);
                $merge_status = 'preview';

                if ($req->boolean('confirm')) {

                    // Check if there are actually staged changes
                    $hasStagedChanges = shell_exec("cd {$dRepoDir} && \"{$git}\" diff --cached --quiet 2>&1; echo $?");
                    $hasStagedChanges = trim($hasStagedChanges) !== '0';
                    
                    Log::info('Has staged changes: ' . ($hasStagedChanges ? 'YES' : 'NO'));
                    
                    $commitOutput = '';
                    if ($hasStagedChanges) {
                        // Get author info from config
                        $cfg = config('services.bitbucket');
                        $authorName = $cfg['git_author_name'] ?? 'Code Merger Bot';
                        $authorEmail = $cfg['git_author_email'] ?? 'codemerger@example.com';
                        
                        // Properly commit with full multi-line message and explicit author/committer
                        $tmpFile = tempnam(sys_get_temp_dir(), 'gitmsg_');
                        file_put_contents($tmpFile, $srcMsg);

                        // Set both author and committer environment variables
                        $commitCmd = "cd {$dRepoDir} && "
                            . "set GIT_AUTHOR_NAME={$authorName} && "
                            . "set GIT_AUTHOR_EMAIL={$authorEmail} && "
                            . "set GIT_COMMITTER_NAME={$authorName} && "
                            . "set GIT_COMMITTER_EMAIL={$authorEmail} && "
                            . "\"{$git}\" commit -F {$tmpFile} 2>&1";
                        $commitOutput = shell_exec($commitCmd);
                        unlink($tmpFile);
                        
                        Log::info('Commit command: ' . $commitCmd);
                        Log::info('Commit output: ' . $commitOutput);
                    } else {
                        $commitOutput = 'No changes to commit (working tree clean)';
                        Log::info($commitOutput);
                    }

                    // Confirmed: commit and push
                    // Ensure SSH key is used for push
                    $sshCmd = "ssh -i \"{$sshKeyPath}\" -o StrictHostKeyChecking=no";
                    $gitCommitPush = [
                        "cd {$dRepoDir}",
                        "set GIT_SSH_COMMAND={$sshCmd}",
                        "\"{$git}\" push origin {$dBr}"
                    ];
                    $pushOutput = shell_exec(implode(' && ', $gitCommitPush) . ' 2>&1');
                    
                    Log::info('Push output: ' . $pushOutput);

                    $taskComment = "Git branch updated: " . $req->branchB
                                    . "\n\nUpdate(s):"
                                    . "\n1. Synchronized " . $sRepo . " " . $sBr . " into " . $dRepo . " " . $dBr
                                    . "\n2. Merged the latest changes into the relevant demo account";

                    $output = $taskComment . "\n\n=== COMMIT ===\n" . $commitOutput . "\n\n=== PUSH ===\n" . $pushOutput;


                    Log::info('Git push output: ' . $output);
                    Log::info('Git push command: ' . implode(' && ', $gitCommitPush));

                    $merge_status = (strpos($output, 'Everything up-to-date') !== false)
                        ? 'already_synced'
                        : (strpos($output, 'error') !== false || strpos($output, 'fatal') !== false || strpos($output, 'denied') !== false 
                            ? 'merge_failed' 
                            : 'merged');

                    if ($merge_status === 'merged' || $merge_status === 'already_synced') {
                        $log->update(['merge_status' => $merge_status]);
                        // $branchToDeploy = $req->direction === 'AtoB' ? $sBr : $dBr;
                        // DeployBranchJob::dispatch($log->id, $branchToDeploy, $user->id);
                        // $deploy_status = 'queued';
                    }
                }
            } 
            else {
                // ===================================================
                // API MERGE MODE — using Bitbucket app password
                // ===================================================
                $cfg = config('services.bitbucket');
                $payload = [
                    'title' => "Sync $sRepo:$sBr → $dRepo:$dBr (by {$user->email})",
                    'source' => [
                        'branch' => ['name' => $sBr],
                        'repository' => ['full_name' => "{$sWorkspace}/{$sRepo}"]
                    ],
                    'destination' => [
                        'branch' => ['name' => $dBr]
                    ],
                    'close_source_branch' => false
                ];

                $url = "https://api.bitbucket.org/2.0/repositories/{$dWorkspace}/{$dRepo}/pullrequests";
                $res = Http::withBasicAuth($cfg['user'], $cfg['app_password'])->post($url, $payload);

                if ($res->successful()) {
                    $pr = $res->json();
                    $pr_link = $pr['links']['html']['href'] ?? null;
                    $merge_status = 'pr_created';
                    $mergeUrl = "https://api.bitbucket.org/2.0/repositories/{$dWorkspace}/{$dRepo}/pullrequests/{$pr['id']}/merge";
                    $mres = Http::withBasicAuth($cfg['user'], $cfg['app_password'])->post($mergeUrl);

                    if ($mres->successful()) {
                        $merge_status = 'merged';
                        $log->update(['merge_status' => $merge_status, 'pr_link' => $pr_link]);
                        $branchToDeploy = $req->direction === 'AtoB' ? $sBr : $dBr;
                        DeployBranchJob::dispatch($log->id, $branchToDeploy, $user->id);
                        $deploy_status = 'queued';
                    } else {
                        $merge_status = 'merge_failed';
                    }
                } else {
                    $merge_status = 'pr_failed';
                }
            }
        } catch (\Exception $e) {
            $merge_status = 'error';
            Log::error('Merge failed: ' . $e->getMessage());
        }

        $log->update([
            'pr_link' => $pr_link,
            'merge_status' => $merge_status,
            'deploy_status' => $deploy_status
        ]);

        if ($req->ajax() || $req->wantsJson()) {
            return response()->json([
                'status' => $merge_status,
                'log_id' => $log->id,
                'pr_link' => $pr_link,
                'deploy_status' => $deploy_status,
                'output' => $output ?? '',
                'report_url' => url('/report')
            ]);
        }

        return redirect('/report')->with('status', 'Merge requested.');
    }

    public function report()
    {
        $logs = MergeLog::with('user')->orderByDesc('id')->paginate(50);
        return view('report', compact('logs'));
    }

    private function ensureRepoAndBranch(string $baseDir, string $workspace, string $repo, string $branch, string $sshKeyPath): string
    {
        $git = $this->getGitPath();
        $repoDir = rtrim($baseDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $repo;
        if (!file_exists($repoDir . DIRECTORY_SEPARATOR . '.git')) {
            // clone directly, parent directory will be created by git
            $parent = dirname($repoDir);
            if (!file_exists($parent)) mkdir($parent, 0775, true);
            $cloneCmd = "\"{$git}\" clone git@bitbucket.org:{$workspace}/{$repo}.git {$repoDir}";
            shell_exec($cloneCmd . ' 2>&1');
        }

        $currentBranch = trim(shell_exec("cd {$repoDir} && \"{$git}\" rev-parse --abbrev-ref HEAD 2>NUL"));

        // Switch if needed
        if ($currentBranch !== $branch) {
            shell_exec("cd {$repoDir} && \"{$git}\" fetch origin 2>NUL");
            shell_exec("cd {$repoDir} && (\"{$git}\" checkout {$branch} 2>NUL || \"{$git}\" checkout -b {$branch} origin/{$branch}) 2>NUL");
        }

        // Always pull latest
        shell_exec("cd {$repoDir} && \"{$git}\" pull origin {$branch} 2>&1");
        
        // $commands = [
        //     "cd {$repoDir}",
        //     "git fetch origin",
        //     "if [ \"$(git rev-parse --abbrev-ref HEAD)\" != \"{$branch}\" ]; then
        //          git checkout {$branch} 2>/dev/null || git checkout -b {$branch} origin/{$branch};
        //      fi;
        //      git pull origin {$branch}"
        // ];
        
        // shell_exec(implode(' && ', $commands) . ' 2>&1');
        return $repoDir;
    }

    private function syncWorkingTree(string $sourceDir, string $destDir): void
    {
        // Remove everything in destination except .git
        $items = scandir($destDir);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..' || $item === '.git') continue;
            $path = $destDir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path)) {
                \File::deleteDirectory($path);
            } else {
                @unlink($path);
            }
        }
        // Copy everything from source except .git
        $this->copyDir($sourceDir, $destDir, ['.git']);
    }

    private function copyDir(string $src, string $dst, array $ignore = []): void
    {
        $dir = opendir($src);
        @mkdir($dst, 0775, true);
        while(false !== ($file = readdir($dir))) {
            if ($file === '.' || $file === '..') continue;
            if (in_array($file, $ignore, true)) continue;
            $srcPath = $src . DIRECTORY_SEPARATOR . $file;
            $dstPath = $dst . DIRECTORY_SEPARATOR . $file;
            if (is_dir($srcPath)) {
                $this->copyDir($srcPath, $dstPath, $ignore);
            } else {
                copy($srcPath, $dstPath);
            }
        }
        closedir($dir);
    }

    /**
     * Get the full path to the git executable
     * @return string
     * @throws \Exception if git is not found
     */
    private function getGitPath(): string
    {
        // Check if git is already in PATH
        $gitPath = trim(shell_exec('where git 2>NUL') ?? '');
        if (!empty($gitPath)) {
            // where returns multiple paths, get the first one
            $paths = explode("\r\n", $gitPath);
            $resolvedPath = trim($paths[0]);
            Log::info('Git found in PATH: ' . $resolvedPath);
            return $resolvedPath;
        }

        Log::warning('Git not found in PATH, checking common locations...');

        // Common Git installation paths on Windows
        $commonPaths = [
            'C:\\laragon\\bin\\git\\bin\\git.exe',
            'C:\\Program Files\\Git\\bin\\git.exe',
            'C:\\Program Files (x86)\\Git\\bin\\git.exe',
        ];

        foreach ($commonPaths as $path) {
            if (file_exists($path)) {
                Log::info('Git found at: ' . $path);
                return $path;
            }
        }

        // Git not found anywhere - throw exception with helpful message
        $errorMsg = "Git executable not found. Please either:\n"
            . "1. Add git to your system PATH (C:\\laragon\\bin\\git\\bin), OR\n"
            . "2. Install Git for Windows from https://git-scm.com/download/win\n"
            . "Then restart Laragon/Apache.";
        
        Log::error('Git not found: ' . $errorMsg);
        throw new \Exception($errorMsg);
    }
}
