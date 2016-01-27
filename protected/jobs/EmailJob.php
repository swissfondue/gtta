<?php
/**
 * Class EmailJob
 */
class EmailJob extends BackgroundJob {
    /**
     * Sends emails from the database queue.
     */
    private function _sendEmail($userId, $subject, $content) {
        $system = System::model()->findByPk(1);

        // Yii-mail transport options
        $transportOptions = array(
            "host" => $system->mail_host,
            "port" => $system->mail_port,
            "username" => $system->mail_username,
            "password" => $system->mail_password,
        );

        if (
            !$system->email ||
            !$transportOptions['host'] ||
            !$transportOptions['port'] ||
            !$transportOptions['username'] ||
            !$transportOptions['password']
        ) {
            throw new Exception('Invalid mail settings.');
        }

        $systemMail = $system->email;
        $transportOptions['encryption'] = $system->mail_encryption ? 'ssl' : null;
        Yii::app()->mail->transportOptions = $transportOptions;

        $user = User::model()->findByPk($userId);

        if (!$user) {
            throw new Exception("User not found.");
        }

        try {
            $message          = new YiiMailMessage();
            $message->from    = array($systemMail => Yii::app()->name);
            $message->to      = $user->email;
            $message->subject = $subject;
            $message->setBody($content, 'text/html', 'utf-8');

            Yii::app()->mail->send($message);
        }
        catch (Exception $e)
        {
            // ignore
        }
    }

    /**
     * Run
     * @param array $args
     */
    public function perform() {
        try {
            if (!isset($this->args['user_id']) || !isset($this->args['subject']) || !isset($this->args['content'])) {
                throw new Exception("Invalid job params.");
            }

            $this->_sendEmail($this->args['user_id'], $this->args['subject'], $this->args['content']);
        } catch (Exception $e) {
            $this->log($e->getMessage(), $e->getTraceAsString());
        }
    }
}