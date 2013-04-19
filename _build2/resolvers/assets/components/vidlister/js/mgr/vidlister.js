var VidLister = function(config) {
    config = config || {};
    VidLister.superclass.constructor.call(this,config);
};
Ext.extend(VidLister,Ext.Component,{
    page:{},window:{},grid:{},tree:{},panel:{},combo:{},config: {}
});
Ext.reg('vidlister',VidLister);

VidLister = new VidLister();