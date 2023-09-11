mw.loader.using('ext.Adiutor.csd', function () {   
    mw.hook('csdJsonLoaded').add(function () {
        fetchApiData(function (jsonData) {
            if (!jsonData) {
                mw.notify('MediaWiki:Adiutor-CSD.json data is empty or undefined.', {
                    title: mw.msg('operation-failed'),
                    type: 'error'
                });
                return;
            }
            var csdData = jsonData.someProperty;
            console.log(csdData);
        });
    });
});