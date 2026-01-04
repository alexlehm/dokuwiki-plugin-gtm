<?php

use dokuwiki\Extension\ActionPlugin;
use dokuwiki\Extension\EventHandler;

class action_plugin_googletagmanager extends ActionPlugin
{
    public const GTMID = 'GTMID';

    /**
     * Register its handlers with the DokuWiki's event controller
     */
    public function register(EventHandler $controller)
    {
        $controller->register_hook('TPL_METAHEADER_OUTPUT', 'BEFORE', $this, 'addHeaders');
    }

    public function addHeaders(&$event, $param)
    {
            $GTMID = $this->getConf(self::GTMID);
            if (!$GTMID) return;

            $is_AW_tag = substr($GTMID, 0, 3) == 'AW-';

        if ($is_AW_tag) {
            $event->data['script'][] = ['src' => "https://www.googletagmanager.com/gtag/js?id=" . $GTMID];
            $event->data['script'][] = ['type' => 'text/javascript',
                '_data' => "window.dataLayer = window.dataLayer || []; function gtag(){dataLayer.push(arguments);}" .
                " gtag('js', new Date()); gtag('config', '" .
                $GTMID .
                "');"];
        } else {
            $event->data['noscript'][] = ['_data' => '<iframe src="https://www.googletagmanager.com/ns.html?id=' .
                $GTMID .
                '" height="0" width="0" style="display:none;visibility:hidden"></iframe>'];
            $event->data['script'][] = ['type' => 'text/javascript',
                '_data' => "(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','" .
                $GTMID .
                "');"];
        }
    }
}
