<?php

/**
 * Reference manager class
 */
class ReferenceManager {
    /**
     * Serialize and share reference
     * @param Reference $reference
     * @throws Exception
     */
    public function share(Reference $reference) {
        $system = System::model()->findByPk(1);

        $data = [
            "name" => $reference->name,
            "url" => $reference->url,
        ];

        try {
            $api = new CommunityApiClient($system->integration_key);
            $reference->external_id = $api->shareReference(["reference" => $data])->id;
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
        $r = Reference::model()->findByAttributes(["external_id" => $reference->id]);

        if (!$r) {
            $r = new Reference();
        }

        $r->external_id = $reference->id;
        $r->name = $reference->name;
        $r->url = $reference->url;
        $r->status = Reference::STATUS_INSTALLED;
        $r->save();

        return $r;
    }
}
