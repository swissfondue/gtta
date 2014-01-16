<?php

/**
 * VM manager
 */
class VMManager {
    const CONTROL_COMMAND = "/usr/sbin/vzctl";
    const ID = 666;
    const TEMPLATE = "debian-7.0-i386-minimal";
    const CONFIG = "basic";
    const IP = "192.168.66.66";
    const HOSTNAME = "gtta";
    const CPU_UNITS = 1000;
    const CPU_LIMIT = 50;
    const DISK_LIMIT = "5G";
    const MEMORY_LIMIT = "1G";
    const OPENVZ_PRIVATE_DIR = "/var/lib/vz/private";
    const TOOLS_DIRECTORY = "tools";
    const INSTALL_SCRIPTS_DIRECTORY = "install";
    const RUN_SCRIPT = "run_script.py";

    /**
     * Run VM command
     * @param $command
     * @param $params
     */
    private function _command($command, $params=null, $throwException=true) {
        if (is_array($params)) {
            $newParams = array();

            foreach ($params as $k => $v) {
                $newParams[] = "--" . $k . " " . $v;
            }

            $params = implode(" ", $newParams);
        }

        if ($command == "exec") {
            $command = "exec2";
            $params = '"' . $params . '"';
        }

        $command = self::CONTROL_COMMAND . " $command " . self::ID;

        if ($params) {
            $command = "$command $params";
        }

        return ProcessManager::runCommand($command, $throwException);
    }

    /**
     * Stop and destroy the VM
     */
    private function _stopAndDestroy() {
        try {
            $this->_command("stop");
            $this->_command("destroy", array("wait" => 1));
        } catch (Exception $e) {
            // VM container may not exist at this step
        }
    }

    /**
     * Run command in VM container
     * @param $command
     * @param $throwException
     * @return string result
     */
    public function runCommand($command, $throwException=true) {
        return $this->_command("exec", $command, $throwException);
    }

    /**
     * Kill process group
     * @param $groupId integer process group id
     */
    public function killProcessGroup($groupId) {
        if (!$groupId) {
            return;
        }

        try {
            $this->runCommand("kill -9 -" . $groupId);
        } catch (Exception $e) {
            // pass
        }
    }

    /**
     * Check if VM is running
     */
    public function isRunning() {
        try {
            ProcessManager::runCommand(self::CONTROL_COMMAND . " status  " . self::ID . " | grep running");
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Virtualize path
     * @param $path
     * @return string
     */
    public function virtualizePath($path) {
        return self::OPENVZ_PRIVATE_DIR . "/" . self::ID . $path;
    }

    /**
     * Regenerate virtual machine
     */
    public function regenerate() {
        $this->_stopAndDestroy();

        $this->_command("create", array(
            "ostemplate" => self::TEMPLATE,
            "config" => self::CONFIG,
            "hostname" => self::HOSTNAME,
            "ipadd" => self::IP,
        ));

        try {
            $nameservers = ProcessManager::runCommand("cat /etc/resolv.conf | grep nameserver | cut -d \" \" -f 2");

            if (!$nameservers) {
                throw new Exception("No nameservers found");
            }

            $nameservers = explode("\n", $nameservers);
            $nameserver = $nameservers[0];

            $this->_command("set", array(
                "cpuunits" => self::CPU_UNITS,
                "cpulimit" => self::CPU_LIMIT . "%",
                "nameserver" => $nameserver,
                "diskspace" => self::DISK_LIMIT,
                "privvmpages" => self::MEMORY_LIMIT,
                "noatime" => "yes",
                "save" => "",
            ));

            $this->_command("start");

            // waiting for VM to start
            sleep(60);

            // change APT sources
            $this->runCommand("echo \"deb http://ftp.de.debian.org/debian wheezy main contrib non-free\" > /etc/apt/sources.list");
            $this->runCommand("echo \"deb http://security.debian.org/ wheezy/updates main contrib non-free\" >> /etc/apt/sources.list");
            $this->runCommand("apt-get -y update");

            $scriptsPath = Yii::app()->params["packages"]["path"]["user"]["scripts"];
            $filesPath = Yii::app()->params["automation"]["filesPath"];

            FileManager::createDir($this->virtualizePath($scriptsPath), 0777, true);
            FileManager::createDir($this->virtualizePath($filesPath), 0777, true);
            FileManager::copyRecursive($scriptsPath, $this->virtualizePath($scriptsPath));

            FileManager::copy(
                implode("/", array(VERSION_DIR, self::TOOLS_DIRECTORY, self::INSTALL_SCRIPTS_DIRECTORY, self::RUN_SCRIPT)),
                $this->virtualizePath(BASE_DIR . "/" . self::RUN_SCRIPT)
            );

            // install dependencies
            $pm = new PackageManager();
            $pm->installAllDependencies();
        } catch (Exception $e) {
            $this->_stopAndDestroy();
            throw $e;
        }
    }
}