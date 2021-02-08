//Add field retailcrm_delivery_code to minishop2's getFields()
Ext.override(miniShop2.grid.Delivery, {
    getParentFields: miniShop2.grid.Delivery.prototype.getFields(),
    getFields: function () {
        var parentFields = this.getParentFields;
        parentFields.push('retailcrm_delivery_code');
        return parentFields;
    },

});

//Add retailcrm_delivery_code field to minishop2's delivery setting window
Ext.ComponentMgr.onAvailable('minishop2-window-delivery-update', function (config) {
    this.fields[0]['items'][0]['items'].push(
        {
            xtype: 'textfield',
            name: 'retailcrm_delivery_code',
            fieldLabel: _('retailcrm_delivery_code'),
            anchor: '99%',
            id: 'minishop2-window-delivery-update-retailcrm_delivery_code'

        }
    );
});


//Add field retailcrm_payment_code to minishop2's getFields()
Ext.override(miniShop2.grid.Payment, {
    getParentFields: miniShop2.grid.Payment.prototype.getFields(),
    getFields: function () {
        var parentFields = this.getParentFields;
        parentFields.push('retailcrm_payment_code');
        return parentFields;
    },

});

//Add retailcrm_payment_code field to minishop2's delivery setting window
Ext.ComponentMgr.onAvailable('minishop2-window-payment-update', function (config) {
    this.fields[0]['items'][0]['items'].push(
        {
            xtype: 'textfield',
            name: 'retailcrm_payment_code',
            fieldLabel: _('retailcrm_payment_code'),
            anchor: '99%',
            id: 'minishop2-window-payment-update-retailcrm_payment_code'

        }
    );
});


//Add field retailcrm_status_code to minishop2's getFields()
Ext.override(miniShop2.grid.Status, {
    getParentFields: miniShop2.grid.Status.prototype.getFields(),
    getFields: function () {
        var parentFields = this.getParentFields;
        parentFields.push('retailcrm_status_code');
        return parentFields;
    },

});


//Add retailcrm_status_code field to minishop2's delivery setting window
Ext.ComponentMgr.onAvailable('minishop2-window-status-update', function (config) {
    this.fields.push(
        {
            xtype: 'textfield',
            name: 'retailcrm_status_code',
            fieldLabel: _('retailcrm_status_code'),
            anchor: '99%',
            id: 'minishop2-window-status-update-retailcrm_status_code'

        }
    );
});