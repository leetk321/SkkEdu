
function popup() {
    this.name = 'popup';
    this.type = 'layer';
    this.position = 'center';
    this.width = 400;
    this.pos_top = 100;
    this.pos_left = 100;
    this.padding = 0;
    this.effect_speed = 500;

    this.show = function() {
        if (this.checkHideCookie()) return;

        jQuery("#addon-popup-layer").css('display', 'block');

        if (this.width == 0)
            this.width = jQuery('#addon-popup-image').width();

        var target_selector = '#addon-popup-layer';
        if (this.type == 'modal') target_selector = '#addon-popup-container';

        if (this.width) {
            jQuery(target_selector).width(this.width);
            jQuery(target_selector).css('top', this.pos_top);
            jQuery("#addon-popup-content").css('padding', this.padding);


            if (this.position == 'center') {
                jQuery(target_selector).css('left', '50%');
                jQuery(target_selector).css('margin-left', Math.round(this.width / 2) * -1);
            }
            else if (this.position == 'right')
                jQuery(target_selector).css('right', this.pos_left);
            else
                jQuery(target_selector).css('left', this.pos_left);
        }
        else {
            jQuery("#addon-popup-layer").css('display', 'none');
        }
    };

    this.close = function() {
        if (jQuery("#addon-popup-hide-check").attr('checked')) this.setHideCookie();
        this.hide();
    };

    this.checkHide = function() {
        if (jQuery("#addon-popup-hide-check").attr('checked')) {
            this.setHideCookie();
            this.hide();
        }
    };

    this.hide = function() {
        //jQuery("#addon-popup-layer").css('display', 'none');
        jQuery("#addon-popup-layer").fadeOut(this.effect_speed);
    };

    this.checkHideCookie = function() {
        var val_hide = xGetCookie('_hide_popup_today_');
        if (val_hide == 'true') return true;
        return false;
    }

    this.setHideCookie = function() {
        var expire = new Date();
        expire.setTime(expire.getTime() + (1000 * 60 * 60 * 24)); // 24 hours
        xSetCookie('_hide_popup_today_', 'true', expire);
    }

};

var this_popup = new popup();

jQuery(window).load(function() {
    this_popup.show();
});
