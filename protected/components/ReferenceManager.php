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
            CommunityShareJob::enqueue(array(
                'type' => CommunityShareJob::TYPE_REFERENCE,
                'obj_id' => $reference->id,
            ));
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

        try {
            $api = new CommunityApiClient($system->integration_key);
            $reference->external_id = $api->shareReference(array("reference" => $data))->id;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        $reference->status = Reference::STATUS_INSTALLED;
        $reference->save();
    }

    /**
     * Create reference
     * @param $reference
     * @return Reference
     * @throws Exception
     */
    public function create($reference, $initial) {
        /** @var System $system */
        $system = System::model()->findByPk(1);
        $api = new CommunityApiClient($initial ? null : $system->integration_key);
        $reference = $api->getReference($reference)->reference;

        $id = $reference->id;
        $existingReference = Reference::model()->findByAttributes(array("external_id" => $id));

        if ($existingReference) {
            return $existingReference;
        }

        $r = new Reference();
        $r->external_id = $reference->id;
        $r->name = $reference->name;
        $r->url = $reference->url;
        $r->status = Reference::STATUS_INSTALLED;
        $r->save();

        return $r;
    }
}
