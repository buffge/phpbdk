<?php
/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2019/1/27
 * Time: 21:08
 */

namespace bdk\app\common\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\facade\Env;
use Swoole\Process;

class SwServer extends Command
{
    protected $config = [
        'host'      => '0.0.0.0',
        'port'      => 7777,
        'daemonize' => false,
        'pid_file'  => '',
    ];

    protected function configure()
    {
        $this->setName('react 构建服务')
            ->addArgument('action', Argument::OPTIONAL, "start|stop|restart|reload", 'start')
            ->addOption('host', 'H', Option::VALUE_OPTIONAL, '服务器的域名', null)
            ->addOption('port', 'p', Option::VALUE_OPTIONAL, '服务器的端口', null)
            ->addOption('daemon', 'd', Option::VALUE_NONE, '守护模式')
            ->setDescription('react 构建程序,执行yarn build');
        $this->config['pid_file'] = Env::get('runtime_path') . 'swoole.pid';
    }

    protected function execute(Input $input, Output $output)
    {
        $action = $input->getArgument('action');
        $this->init();
        if (in_array($action, ['start', 'stop', 'reload', 'restart'])) {
            $this->output->writeln("你选择了{$action}");
            $this->$action();
        } else {
            $output->writeln("<error>错误的启动参数:{$action}, 期望得到 start|stop|restart|reload .</error>");
        }
    }

    private function init()
    {
        $this->config['port']      = $this->getPort();
        $this->config['host']      = $this->getHost();
        $this->config['daemonize'] = $this->input->hasOption('daemon');
        $this->config['pid_file']  .= '_' . $this->config['port'];
    }

    protected function getHost()
    {
        if ($this->input->hasOption('host')) {
            $host = $this->input->getOption('host');
        } else {
            $host = $this->config['host'];
        }
        return $host;
    }

    protected function getPort()
    {
        if ($this->input->hasOption('port')) {
            $port = $this->input->getOption('port');
        } else {
            $port = $this->config['port'];
        }

        return $port;
    }

    private function start()
    {
        $pid = $this->getMasterPid();
        if ($this->isRunning($pid)) {
            $this->output->writeln("<error>端口{$this->getPort()}服务进程正在运行.</error>");
            return false;
        }
        $this->output->writeln('正在开启Http服务器...');
    }

    private function stop()
    {

    }

    private function restart()
    {

    }

    private function reload()
    {

    }

    /**
     * 获取主进程PID
     * @access protected
     * @return int
     */
    protected function getMasterPid()
    {
        $pidFile = $this->config['pid_file'];

        if (is_file($pidFile)) {
            $masterPid = (int)file_get_contents($pidFile);
        } else {
            $masterPid = 0;
        }

        return $masterPid;
    }

    /**
     * 删除PID文件
     * @access protected
     * @return void
     */
    protected function removePid()
    {
        $masterPid = $this->config['pid_file'];
        if (is_file($masterPid)) {
            unlink($masterPid);
        }
    }

    /**
     * 判断PID是否在运行
     * @access protected
     * @param  int $pid
     * @return bool
     */
    protected function isRunning($pid)
    {
        if (empty($pid)) {
            return false;
        }
        return Process::kill($pid, 0);
    }
}