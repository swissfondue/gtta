<?php
/**
 * Class EmailJob
 */
class EmailJob extends BackgroundJob {
    /**
     * Sends emails from the database queue.
     */
    private function _sendEmail($userId, $subject, $content)
    {
        $user = User::model()->findByPk($userId);

        if (!$user) {
            throw new Exception("User not found.");
        }

        try {
            $message          = new YiiMailMessage();
            $message->from    = array( Yii::app()->params['email']['systemEmail'] => Yii::app()->name);
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
        if (!isset($this->args['user_id']) || !isset($this->args['subject']) || !isset($this->args['content'])) {
            throw new Exception("Invalid job params.");
        }

        $this->_sendEmail($this->args['user_id'], $this->args['subject'], $this->args['content']);
    }
}