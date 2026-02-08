<?php

namespace App\Jobs;

use App\Models\MergeLog;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class DeployBranchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $logId;
    protected $branch;
    protected $userId;

    public function __construct($logId, $branch, $userId)
    {
        $this->logId = $logId;
        $this->branch = $branch;
        $this->userId = $userId;
    }

    public function handle()
    {
        $log = MergeLog::find($this->logId);
        if (!$log) { 
            Log::error('Deploy failed: Log ID not found [' . $this->logId . ']');
            return;
        }

        $cfg = config('services.deploy');
        $sshHost = $cfg['ssh_host'];
        $sshUser = $cfg['ssh_user'];
        $sshKey  = $cfg['ssh_key_path'];
        $script  = $cfg['script_path'];
        $appPath = $cfg['app_path'];

        // 1️⃣ Check SSH key
        if (!file_exists($sshKey)) {
            $log->update([
                'deploy_status' => 'failed',
                'deploy_log'    => "❌ SSH key not found: {$sshKey}",
            ]);
            Log::error('Deploy failed: SSH key not found [' . $sshKey . ']');
            return;
        }

        // 2️⃣ Test SSH connection before continuing
        $log->update([
            'deploy_status' => 'testing_connection',
            'deploy_log'    => "🔍 Testing SSH connection to {$sshHost}...\n",
        ]);

        $checkCmd = "ssh -tt -i {$sshKey} -o BatchMode=yes -o StrictHostKeyChecking=no -o ConnectTimeout=5 {$sshUser}@{$sshHost} \"echo ok\"";
        $checkProcess = Process::fromShellCommandline($checkCmd);
        $checkProcess->setTimeout(10);
        $checkProcess->run();

        Log::error('Deploy Output: ' . $checkProcess->getOutput());
        Log::error('Deploy Error Output: ' . $checkProcess->getErrorOutput());

        if (!$checkProcess->isSuccessful() || trim($checkProcess->getOutput()) !== 'ok') {
            $log->update([
                'deploy_status' => 'failed',
                'deploy_log'    => "❌ SSH connection failed.\n" . $checkProcess->getErrorOutput(),
            ]);
            Log::error('Deploy failed: SSH connection failed[' . $checkProcess->getErrorOutput() . ']');
            return;
        }

        // 3️⃣ SSH connection succeeded — proceed with deployment
        $log->update([
            'deploy_status' => 'running',
            'deploy_log'    => "✅ SSH connection established.\n🚀 Starting deployment...\n",
        ]);

        // 4️⃣ Build and run your actual deployment command
        // $cmd = "{$script} {$appPath} {$this->branch}";
        // $sshCmd = "ssh -i {$sshKey} -o StrictHostKeyChecking=no {$sshUser}@{$sshHost} '{$cmd}'";

        // $process = Process::fromShellCommandline($sshCmd);
        // $process->setTimeout(600);

        // $fullLog = '';

        // $process->run(function ($type, $buffer) use (&$fullLog, $log) {
        //     $timestamp = now()->format('H:i:s');
        //     $line = "[{$timestamp}] " . trim($buffer) . "\n";
        //     $fullLog .= $line;

        //     // Append logs progressively
        //     $log->update(['deploy_log' => \DB::raw("CONCAT(deploy_log, " . \DB::getPdo()->quote($line) . ")")]);
        // });

        // if ($process->isSuccessful()) {
        //     $log->update([
        //         'deploy_status' => 'success',
        //         'deploy_log'    => $fullLog . "\n✅ Deployment completed successfully.\n",
        //     ]);
        // } else {
        //     $log->update([
        //         'deploy_status' => 'failed',
        //         'deploy_log'    => $fullLog . "\n❌ Deployment failed.\n" . $process->getErrorOutput(),
        //     ]);
        // }
    }

}
