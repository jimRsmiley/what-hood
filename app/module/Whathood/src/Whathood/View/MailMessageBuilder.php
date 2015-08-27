<?php

namespace Whathood\View;

class MailMessageBuilder {

    public static function buildNewUserPolygon($userPolygon) {

        $subject = sprintf("'%s' User - Neigborhood Added",
            $userPolygon->getNeighborhood()->getName());
        $body = sprintf("by user %s",
            $userPolygon->getWhathoodUser()->__toString());

        return array('subject' => $subject, 'body' => $body);
    }
}
