<?php

class action_plugin_googletagmanager extends DokuWiki_Action_Plugin
{
    public const GTMID = 'GTMID';

    /**
     * return some info
     */
    public function getInfo()
    {
        return array(
            'author' => 'Alexander Lehmann',
            'email'  => 'alexlehm@gmail.com',
            'date'   => '2026-01-03',
            'name'   => 'Google Tag Manager Plugin',
            'desc'   => 'Plugin to embed Google Tag Manager in your wiki.',
            'url'    => 'https://wiki.lehmann.cx/projects:dokuwiki_gtm',
        );
    }

        /**
         * Register its handlers with the DokuWiki's event controller
         */
    public function register(Doku_Event_Handler $controller)
    {
        $controller->register_hook('TPL_METAHEADER_OUTPUT', 'BEFORE', $this, 'addHeaders');
    }

    public function addHeaders(&$event, $param)
    {
            $GTMID = $this->getConf(self::GTMID);
            if (!$GTMID) return;

            $is_AW_tag = substr($GTMID, 0, 3) == 'AW-';

        if ($is_AW_tag) {
            $event->data['script'][] = array (
                'src' => "https://www.googletagmanager.com/gtag/js?id=" . $GTMID,
            );
            $event->data['script'][] = array (
                'type' => 'text/javascript',
                '_data' => "window.dataLayer = window.dataLayer || []; function gtag(){dataLayer.push(arguments);}" .
                    " gtag('js', new Date()); gtag('config', '" .
                    $GTMID .
                    "');",
            );
        } else {
            $event->data['noscript'][] = array (
                '_data' => '<iframe src="https://www.googletagmanager.com/ns.html?id=' .
                    $GTMID .
                    '" height="0" width="0" style="display:none;visibility:hidden"></iframe>',
            );
            $event->data['script'][] = array (
                'type' => 'text/javascript',
                '_data' => "(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','" .
                    $GTMID .
                    "');",
            );
        }
    }
}
