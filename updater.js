var updater = Class.create({
    initialize: function(divToUpdate, interval, file) {
        this.divToUpdate = divToUpdate;
        this.interval = interval;
        this.file = file;
        new PeriodicalExecuter(this.getUpdate.bind(this), this.interval);
    },
    
    getUpdate: function() {
        var div = this.divToUpdate;
        var interval = this.interval;
        var file = this.file;            
        var oOptions = {
            method: "POST",
            asynchronous: true,
            parameters: "intervalPeriod="+interval,
            onComplete: function (oXHR, Json) {
            	$(div).innerHTML = oXHR.responseText;
            }
        };
        var oRequest = new Ajax.Updater(div, file, oOptions);
    }
});
