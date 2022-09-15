require.config({
    paths: {
        'jquery-colorpicker': '../addons/miniform/js/jquery.colorpicker.min',
    },
    shim: {
        'jquery-colorpicker': {
            deps: ['jquery'],
            exports: '$.fn.extend'
        }
    }
});
