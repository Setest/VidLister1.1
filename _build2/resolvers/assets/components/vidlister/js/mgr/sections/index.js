Ext.onReady(function() {
    MODx.load({ xtype: 'vidlister-page-home'});
});

VidLister.page.Home = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        components: [{
            xtype: 'vidlister-panel-home'
            ,renderTo: 'vidlister-panel-home-div'
        }]
    });
    VidLister.page.Home.superclass.constructor.call(this,config);
};
Ext.extend(VidLister.page.Home,MODx.Component);
Ext.reg('vidlister-page-home',VidLister.page.Home);