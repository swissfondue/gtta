<?php

/**
 * Reference manager class
 */
class ReferenceManager {
    /**
     * Prepare reference sharing
     * @param Reference $reference
     * @throws Exception
     */
    public function prepareSharing(Reference $reference) {
        if (!$reference->external_id) {
            $reference->status = Reference::STATUS_SHARE;
            $reference->save();
        }
    }

    /**
     * Serialize and share reference
     * @param Reference $reference
     * @throws Exception
     */
    public function share(Reference $reference) {
        /** @var System $system */
        $system = System::model()->findByPk(1);

        $data = array(
            "name" => $reference->name,
            "url" => $reference->url,
        );

        $api = new CommunityApiClient($system->integration_key);
        $reference->external_id = $api->shareReference(array("reference" => $data))->id;
        $reference->status = Reference::STATUS_INSTALLED;
        $reference->save();
    }
}
