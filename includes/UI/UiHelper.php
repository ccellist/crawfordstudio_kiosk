<?php

/*
 * Created on Dec 30, 2011
 *
 * author: arturo
 * 
 */

class UI_UiHelper {

    private static $sidebarLinks = array();

    public static function drawBanner($logoUrl, $isMobile = false) {
        $banner = new UI_Banner($logoUrl, $isMobile);
        $banner->draw();
        $banner = null;
    }

    public static function drawStoreHNav($isMobile = false, $isAdmin = false) {
        $navname = "storePrimaryNav";
        $nav = new UI_NavBar($navname, $isMobile);
        $count = 1;
        $navObjs = array();
        if (!$isAdmin) {
            $links = array(
                "Store" => "/index.php?module=StoreFront",
                "Admin" => "/index.php?module=StoreAdmin"
            );
        } else {
            $links = array(
                "Store" => "/index.php?module=StoreFront",
                "Admin" => "/index.php?module=StoreAdmin",
                "" => "",
                " " => "",
                "Logout" => "/index.php?module=User&action=logout"
            );
        }

        foreach ($links as $text => $link) {
            $objLink = new UI_Link($text, $link, $navname . "_" . $count, "hnav");
            $navObjs[] = $objLink;
            unset($objLink);
            $count++;
        }
        if (APP_HAS_CART)
            $navObjs[] = new UI_CartSummary();

        $nav->populate($navObjs);
        $nav->printNav("horizontal");
        unset($nav);
    }

    public static function drawStoreSidebar(array $sidebarLinks = null, $isMobile = false) {
        if (!$isMobile) {
            $sbid = "store_sb";
            $sidebar = new UI_StoreSidebar($sbid);
            $count = 1;
            if (is_array($sidebarLinks) > 0) {
                foreach ($sidebarLinks as $sbLink) {
                    $sidebar->addItem($sbLink);
                    $count++;
                }
            } else {
                $sbLink = new UI_SidebarLink("No links available", "", 0, "empty_link");
                $sidebar->addItem($sbLink);
            }
            $sidebar->printSidebar();
        }
    }

    public static function drawAdminSidebar($isMobile) {
        if (!$isMobile) {
            $sidebar = new UI_AdminSidebar();
            $sidebar->draw();
        }
    }

    public static function drawButton($type, $value, $id, $parentForm, $class = "", array $events = array(), array $images = array()) {
        $btn = new UI_Button($type, $value, $id, $parentForm, $class, $events, $images);
        $btn->printButton();
        unset($btn);
    }

    public static function translateError($errId) {
        $err = new Error($errId);
        print $err->getMessage();
    }

}

