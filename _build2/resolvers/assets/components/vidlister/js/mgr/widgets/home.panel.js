VidLister.panel.Home = function(config) {
    config = config || {};
    Ext.apply(config,{
        border: false
        ,baseCls: 'modx-formpanel'
        ,items: [{
            html: '<h2>'+_('vidlister')+'</h2>'
            ,border: false
            ,cls: 'modx-page-header'
        },{
            xtype: 'modx-tabs'
            ,bodyStyle: 'padding: 10px'
            ,defaults: { border: false ,autoHeight: true }
            ,items: [{
                title: _('vidlister.videos')
                ,border: false
                ,defaults: { autoHeight: true, border: false }
                ,items: [{
                    xtype: 'vidlister-grid-videos'
                    ,preventRender: true
                }]
            }]
        }]
    });
    VidLister.panel.Home.superclass.constructor.call(this,config);
};
Ext.extend(VidLister.panel.Home,MODx.Panel);
Ext.reg('vidlister-panel-home',VidLister.panel.Home);
