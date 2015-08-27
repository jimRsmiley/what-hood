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

    public static function buildSomeoneSearched($search_str) {
        $body = sprintf('<a href="%s">%s</a>', $search_str, $search_str);
        return array(
            'subject' => 'Someone searched for '.$search_str,
            'body'      => $body
        );
    }
}
