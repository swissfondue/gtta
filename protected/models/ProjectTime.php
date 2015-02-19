<?php

/**
 * This is the model class for table "project_time".
 *
 * The followings are the available columns in table 'target_references':
 * @property integer id (pk)
 * @property integer user_id
 * @property integer project_id
 * @property integer time
 * @property string description
 * @property timestamp create_time
 * @property timestamp start_time
 * @property timestamp last_action
 */
class ProjectTime extends ActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return TargetReference the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'project_time';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array( 'user_id, project_id', 'required' ),
            array( 'create_time','default', 'value'=>new CDbExpression('NOW()'), 'setOnEmpty'=>false,'on'=>'insert' )
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'user'    => array( self::BELONGS_TO, 'User',    'user_id' ),
            'project' => array( self::BELONGS_TO, 'Project', 'project_id' ),
        );
    }

    /**
     * Convert seconds to hours
     * @return float
     */
    public function getHours() {
        return round($this->time / 3600, 1);
    }

    /**
     * Return formatted record data
     * @throws Exception
     */
    public function getFormatted() {
        $createTime = new DateTime($this->create_time);
        $startTime  = new DateTime($this->start_time);
        $stopTime   = clone $startTime;
        $stopTime->add(new DateInterval(sprintf("PT%sS", $this->time)));
        $project    = Project::model()->findByPk($this->project_id);
        $total      = $startTime->diff($stopTime);

        if (!$project) {
            throw new Exception("Project not found.");
        }

        $formatted = array(
            "id"          => $this->id,
            "create_time" => $createTime->format("d.m.Y"),
            "project"     => $project->name,
            "start_time"  => $startTime->format("H:i"),
            "stop_time"   => $stopTime->format("H:i"),
            "total"       => $total->format("%H:%I"),
        );

        return $formatted;
    }

    /**
     * Stop time session
     */
    public function stop() {
        $now = new DateTime();
        $started = new DateTime($this->start_time);
        $this->time = $now->getTimestamp() - $started->getTimestamp();

        $this->save();
    }

    /**
     * Get time session duration
     * @return array
     */
    public function getDuration() {
        $now = new DateTime();
        $startTime = new DateTime($this->start_time);
        $diff = $now->diff($startTime);

        return array(
            "hours" => $diff->format("%H"),
            "mins"  => $diff->format("%I"),
        );
    }

    /**
     * Update session last action
     * @return bool|void
     */
    public function updateLastAction() {
        $now = new DateTime();
        $this->last_action = $now->format("Y-m-d G:i:s");
        $this->save();
    }
}
