<?php

/**
 * VM not found exception
 */
class VMNotFoundException extends Exception {}

/**
 * VM manager
 */
class VMManager {
    const CONTROL_COMMAND = "/usr/sbin/vzctl";
    const ID = 666;
    const TEMPLATE = "debian-8.0-x86_64-minimal";
    const CONFIG = "basic";
    const IP = "192.168.66.66";
    const HOSTNAME = "gtta";
    const CPU_UNITS = 1000;
    const CPU_LIMIT = 50;
    const DISK_LIMIT = "5G";
    const MEMORY_LIMIT = "1G";
    const OPENVZ_ROOT_DIR = "/var/lib/vz/root";
    const TOOLS_DIRECTORY = "tools";
    const RUN_SCRIPT = "run_script.py";

    /**
     * If you want to use external OpenVZ Server, do the following:
     * Run on the external OpenVZ Server:
     * 1. Pre-load/create the template: vzctl create 666 --ostemplate debian-8.0-x86_64-minimal --config basic
     * Run on local GTTA server:
     * 1. Install required packages to GTTA-vm: apt-get install fuse sshfs libssh2-1-dev libssh2-php
     * 2. Enable Fuse-module: echo "fuse" >> /etc/modules
     * 3. Enable SSH2 php-plugin: echo "extension=ssh2.so" >> /etc/php5/mods-available/ssh.ini
     * 4. Accept host key of external OpenVZ server: ssh-keyscan openvz_server_host_or_ip >> /etc/ssh/ssh_known_hosts
     * 5. Set all OpenVZ connection configuration values below.
     * 6. Reboot GTTA-vm.
     * 7. Re-create the actual debian container: /opt/gtta/current/web/protected/yiic regenerate 1
     * 8. Install required scripts to the container: /opt/gtta/current/web/protected/yiic initialdata 1 
     */
    const USE_REMOTE_OPENVZ = false;
    const REMOTE_OPENVZ_SERVER = "openvz_server_host_or_ip";
    const REMOTE_OPENVZ_SSH_PORT = 22;
    const REMOTE_OPENVZ_SSH_USER = "";
    const REMOTE_OPENVZ_SSH_PASS = "";
    const REMOTE_OPENVZ_ROOT_DIR = "/vz/root";

    /**
     * Run VM command
     * @param $command
     * @param $params
     * @param $throwException
     * @return string
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

        if (!self::USE_REMOTE_OPENVZ) {
            return ProcessManager::runCommand($command, $throwException);
        }

        $connection = ssh2_connect(self::REMOTE_OPENVZ_SERVER, self::REMOTE_OPENVZ_SSH_PORT);
        ssh2_auth_password($connection, self::REMOTE_OPENVZ_SSH_USER, self::REMOTE_OPENVZ_SSH_PASS);

        // Add exit code to end of stdout as ssh2-lib does not provide an easy way to get it.
        $command = $command . ';echo -e "\n\n$?"; exit';

        $outputstream = ssh2_exec($connection, $command);
        $errorstream = ssh2_fetch_stream($outputstream, SSH2_STREAM_STDERR);
        stream_set_blocking($outputstream, true);
        stream_set_blocking($errorstream, true);

        $output = stream_get_contents($outputstream);
        $error = stream_get_contents($errorstream);
        fclose($outputstream);
        fclose($errorstream);
        unset($connection);

        // Get exit code from end of STDOUT. Then combine STDOUT with STDERR.
        $outputarray = explode("\n\n", $output);
        $exitcode = (int)array_pop($outputarray);
        $output = implode("\n\n", $outputarray) . $error;

        if ($exitcode !== 0 && $throwException) {
            throw new Exception("Invalid result code: $exitcode ($command)");
        }
        return $output;
    }

    /**
     * Stop and destroy the VM
     */
    private function _stopAndDestroy() {
        try {
            $this->_command("stop");
            $this->_command("destroy");
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
            $output = $this->_command("status");
            $exist = strpos($output, self::ID . " exist mounted running") !== false;
            return $exist;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Virtualize path
     * @param $path
     * @return string
     */
    public function virtualizePath($path) {
        if (!self::USE_REMOTE_OPENVZ) {
            return self::OPENVZ_ROOT_DIR . "/" . self::ID . $path;
        }

        $SSHFS_MOUNTPOINT = "/tmp/gtta.openvz-sshfs";
        if (!is_dir($SSHFS_MOUNTPOINT)) {
            FileManager::createDir($SSHFS_MOUNTPOINT, 0777, true);
        }
        if (!is_file($SSHFS_MOUNTPOINT . "/" . self::ID . "/.is-sshfs")) {
            $mountCommand = "echo \"" . self::REMOTE_OPENVZ_SSH_PASS . "\"" .
                " |sshfs -o password_stdin -o cache=no -o idmap=user -o nonempty -p " . self::REMOTE_OPENVZ_SSH_PORT . " " .
                self::REMOTE_OPENVZ_SSH_USER . "@" . self::REMOTE_OPENVZ_SERVER . ":" . self::REMOTE_OPENVZ_ROOT_DIR . " " . $SSHFS_MOUNTPOINT;
                ProcessManager::runCommand($mountCommand);
        }
        return $SSHFS_MOUNTPOINT . "/" . self::ID . $path;
    }

    /**
     * Regenerate virtual machine
     * @param boolean $firstTime
     * @throws Exception
     */
    public function regenerate($firstTime=false) {
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
                "save" => "",
            ));

            $this->_command("start");

            // waiting for VM to start
            sleep(60);

            if (self::USE_REMOTE_OPENVZ) {
                // add a remote filesystem identifier so that we can detect later if the sshfs is already mounted.
                $this->runCommand("touch /.is-sshfs");
            }

            // change APT sources
            $this->runCommand("echo \"deb http://http.de.debian.org/debian jessie main contrib non-free\" > /etc/apt/sources.list");
            $this->runCommand("echo \"deb http://security.debian.org/ jessie/updates main contrib non-free\" >> /etc/apt/sources.list");
            $this->runCommand("apt-get -y update");

            $scriptsPath = Yii::app()->params["packages"]["path"]["scripts"];
            $filesPath = Yii::app()->params["automation"]["filesPath"];

            FileManager::createDir($this->virtualizePath($scriptsPath), 0777, true);
            FileManager::createDir($this->virtualizePath($filesPath), 0777, true);
            FileManager::copyRecursive($scriptsPath, $this->virtualizePath($scriptsPath));

            FileManager::copy(
                implode("/", array(BASE_DIR, "current", self::TOOLS_DIRECTORY, self::RUN_SCRIPT)),
                $this->virtualizePath(BASE_DIR . "/" . self::RUN_SCRIPT)
            );

            if (!$firstTime) {
                $pm = new PackageManager();
                $pm->installAllDependencies();
            }
        } catch (Exception $e) {
            $this->_stopAndDestroy();
            throw $e;
        }
    }
}