<?php

/**
 * Email sender class. 
 */
class EmailCommand extends ConsoleCommand
{
    /**
     * Sends emails from the database queue. 
     */
    private function _sendEmails()
    {
        $system = System::model()->findByPk(1);

        // Yii-mail transport options
        $transportOptions = array(
            "host"          => $system->mail_host,
            "port"          => $system->mail_port,
            "username"      => $system->mail_username,
            "password"      => $system->mail_password,
        );

        if ( !$system->email ||
             !$system->mail_max_attempts ||
             !$transportOptions['host'] ||
             !$transportOptions['port'] ||
             !$transportOptions['username'] ||
             !$transportOptions['password']
           )
        {
            throw new Exception('Invalid mail settings.');
        }

        $maxAttempts = $system->mail_max_attempts;
        $systemMail = $system->email;

        $transportOptions['encryption'] = $system->mail_crypt ? 'ssl' : null;
        Yii::app()->mail->transportOptions = $transportOptions;

        $emails = Email::model()->with('user')->findAll(
            array(
                'condition' => 'NOT sent AND attempts < :max_attempts',
                'params'    => array( 'max_attempts' => $maxAttempts ),
                'order'     => 't.id ASC'
            )
        );
        
        foreach ($emails as $email)
        {
            $email->attempts += 1;
            
            try
            {
                $message          = new YiiMailMessage();
                $message->from    = array( $systemMail => Yii::app()->name );
                $message->to      = $email->user->email;
                $message->subject = $email->subject;
                $message->setBody($email->content, 'text/html', 'utf-8');

                Yii::app()->mail->send($message);
                
                $email->sent = true;
            }
            catch (Exception $e)
            {
                throw new Exception($e->getMessage());
            }
            
            $email->save();

            if ($email->sent)
                $email->delete();
        }
    }

    /**
     * Run
     * @param array $args
     */
    protected function runLocked($args) {
        $this->_sendEmails();
    }
}
