<?php

/**
 * Certificate class
 */
class Certificate {
    /**
     * Generate certificate
     * @param $userId
     */
    public static function generate($userId) {
        $user = User::model()->findByPk($userId);

        $fileName = md5($user->id . time() . rand());
        $password = md5($user->id . time() . rand());
        $name = $user->name ? $user->name : $user->email;
        $email = $user->email;

        $output = array();
        $return = null;

        exec("openssl genrsa -passout pass:$password -out /tmp/$fileName.key 2048", $output, $return);

        if (!$return) {
            $subject = "/CN=$name/emailAddress=$email";

            unset($output);
            exec("openssl req -new -key /tmp/$fileName.key -passin pass:$password -out /tmp/$fileName.csr -subj \"$subject\"", $output, $return);

            if (!$return) {
                unset($output);
                exec("openssl x509 -req -days 365 -in /tmp/$fileName.csr -CA /opt/gtta/ca/ca.crt -CAkey /opt/gtta/ca/ca.key -CAcreateserial -passin pass:$password -out /tmp/$fileName.crt", $output, $return);

                if (!$return) {
                    unset($output);
                    exec("openssl pkcs12 -export -clcerts -out /tmp/$fileName.pfx -in /tmp/$fileName.crt -inkey /tmp/$fileName.key -passin pass:$password -passout pass:gtta", $output, $return);

                    if (!$return) {
                        unset($output);
                        exec("openssl x509 -in /tmp/$fileName.crt -noout -serial", $output, $return);
                        $serial = implode("", $output);

                        if ($serial && strpos($serial, "serial=") !== false) {
                            $serial = substr($serial, strpos($serial, "serial=") + 7);
                        }

                        unset($output);
                        exec("openssl x509 -in /tmp/$fileName.crt -noout -issuer", $output, $return);
                        $issuer = implode("", $output);

                        if ($issuer && strpos($issuer, "issuer=") !== false) {
                            $issuer = substr($issuer, strpos($issuer, "issuer=") + 8);
                        }

                        if ($serial && $issuer) {
                            $user->certificate_serial = $serial;
                            $user->certificate_issuer = $issuer;
                            $user->save();

                            // give user a file
                            header('Content-Description: File Transfer');
                            header('Content-Type: application/octet-stream');
                            header('Content-Disposition: attachment; filename="' . $email . '.pfx"');
                            header('Content-Transfer-Encoding: binary');
                            header('Expires: 0');
                            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                            header('Pragma: public');
                            header('Content-Length: ' . filesize("/tmp/$fileName.pfx"));

                            ob_clean();
                            flush();

                            readfile("/tmp/$fileName.pfx");
                        }

                        @unlink("/tmp/$fileName.pfx");
                    }

                    @unlink("/tmp/$fileName.crt");
                }

                @unlink("/tmp/$fileName.csr");
            }

            @unlink("/tmp/$fileName.key");
        }

        exit(0);
    }
}