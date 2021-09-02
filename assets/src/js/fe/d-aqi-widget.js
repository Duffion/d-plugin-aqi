console.log('loading in widget helper d-aqi');
let $ = jQuery;
let d_aqi = {
    targets: {},
    instance: {},
    readings: {},

    init: function () {
        this.define();

        console.log('Loaded AQI widget', this);
    },

    define: function () {
        this.targets.widget = $('[data-aqinfo-widget]');

        if (this.targets.widget.length > 0) {
            this.instance = JSON.parse(this.targets.widget.data('aqinfo'));

            // lets build our data points //
            this.build.aqi();
            this.build.widget();

            // Add onClick to AQI widget
            var bubble = document.getElementsByClassName('d-aqinfo--bubble');
            for ( const el of bubble ) {
                el.addEventListener( 'click', this.clickAQI, false );
            }
        }
    },
    loading: {
        remove: function () {
            d_aqi.targets.widget.find('.d-loading').remove();
        }

    },
    build: {
        aqi: function () {
            var instance = d_aqi.instance;

            var AQI = d_aqi.AQI.aqiFromPM(instance.p_2_5_um);
            var AQIDescription = d_aqi.AQI.getAQIDescription(AQI); //A short description of the provided AQI
            var AQIMessage = d_aqi.AQI.getAQIMessage(AQI); // What the provided AQI means (a longer description)

            d_aqi.readings.aqi = AQI;
            d_aqi.readings.desc = AQIDescription;
            d_aqi.readings.mesg = AQIMessage;
        },
        widget: function () {
            var readings = d_aqi.readings,
                widget = d_aqi.targets.widget;
            d_aqi.loading.remove();

            console.log('reading', readings);
            widget.find('.d-aqinfo--cir-index').html(readings.aqi);
            widget.find('.d-aqinfo--cir-title').html(readings.desc);
            widget.find('.d-aqinfo--popover').html(readings.mesg);
            widget.find('.d-aqinfo--cir').attr('data-aqinfo-ranking', readings.desc.toLowerCase());
            widget.find('.d-aqinfo--cir-bottom').attr('data-aqinfo-ranking', readings.desc.toLowerCase());
        }
    },
    clickAQI: function(e) {
        window.open( 'https://www.purpleair.com/map?opt=1/mAQI/a10/cC0&key=ONG0MQF1Y4RY4IHO&select=85827#14/39.31224/-120.15584', '_blank' );
    },

    AQI: {
        aqiFromPM: function (pm) {

            if (isNaN(pm)) return "-";
            if (pm == undefined) return "-";
            if (pm < 0) return pm;
            if (pm > 1000) return "-";
            /*      
            Good                              0 - 50         0.0 - 15.0         0.0 – 12.0
            Moderate                          51 - 100           >15.0 - 40        12.1 – 35.4
            Unhealthy for Sensitive Groups    101 – 150     >40 – 65          35.5 – 55.4
            Unhealthy                         151 – 200         > 65 – 150       55.5 – 150.4
            Very Unhealthy                    201 – 300 > 150 – 250     150.5 – 250.4
            Hazardous                         301 – 400         > 250 – 350     250.5 – 350.4
            Hazardous                         401 – 500         > 350 – 500     350.5 – 500
            */
            if (pm > 350.5) {
                return d_aqi.AQI.calcAQI(pm, 500, 401, 500, 350.5);
            } else if (pm > 250.5) {
                return d_aqi.AQI.calcAQI(pm, 400, 301, 350.4, 250.5);
            } else if (pm > 150.5) {
                return d_aqi.AQI.calcAQI(pm, 300, 201, 250.4, 150.5);
            } else if (pm > 55.5) {
                return d_aqi.AQI.calcAQI(pm, 200, 151, 150.4, 55.5);
            } else if (pm > 35.5) {
                return d_aqi.AQI.calcAQI(pm, 150, 101, 55.4, 35.5);
            } else if (pm > 12.1) {
                return d_aqi.AQI.calcAQI(pm, 100, 51, 35.4, 12.1);
            } else if (pm >= 0) {
                return d_aqi.AQI.calcAQI(pm, 50, 0, 12, 0);
            } else {
                return undefined;
            }

        },
        bplFromPM: function (pm) {
            if (isNaN(pm)) return 0;
            if (pm == undefined) return 0;
            if (pm < 0) return 0;
            /*      
                Good                              0 - 50         0.0 - 15.0         0.0 – 12.0
            Moderate                        51 - 100           >15.0 - 40        12.1 – 35.4
            Unhealthy for Sensitive Groups   101 – 150     >40 – 65          35.5 – 55.4
            Unhealthy                                 151 – 200         > 65 – 150       55.5 – 150.4
            Very Unhealthy                    201 – 300 > 150 – 250     150.5 – 250.4
            Hazardous                                 301 – 400         > 250 – 350     250.5 – 350.4
            Hazardous                                 401 – 500         > 350 – 500     350.5 – 500
            */
            if (pm > 350.5) {
                return 401;
            } else if (pm > 250.5) {
                return 301;
            } else if (pm > 150.5) {
                return 201;
            } else if (pm > 55.5) {
                return 151;
            } else if (pm > 35.5) {
                return 101;
            } else if (pm > 12.1) {
                return 51;
            } else if (pm >= 0) {
                return 0;
            } else {
                return 0;
            }
        },
        bphFromPM: function (pm) {
            //return 0;
            if (isNaN(pm)) return 0;
            if (pm == undefined) return 0;
            if (pm < 0) return 0;
            /*      
                Good                              0 - 50         0.0 - 15.0         0.0 – 12.0
            Moderate                        51 - 100           >15.0 - 40        12.1 – 35.4
            Unhealthy for Sensitive Groups   101 – 150     >40 – 65          35.5 – 55.4
            Unhealthy                                 151 – 200         > 65 – 150       55.5 – 150.4
            Very Unhealthy                    201 – 300 > 150 – 250     150.5 – 250.4
            Hazardous                                 301 – 400         > 250 – 350     250.5 – 350.4
            Hazardous                                 401 – 500         > 350 – 500     350.5 – 500
            */
            if (pm > 350.5) {
                return 500;
            } else if (pm > 250.5) {
                return 500;
            } else if (pm > 150.5) {
                return 300;
            } else if (pm > 55.5) {
                return 200;
            } else if (pm > 35.5) {
                return 150;
            } else if (pm > 12.1) {
                return 100;
            } else if (pm >= 0) {
                return 50;
            } else {
                return 0;
            }
        },

        calcAQI: function (Cp, Ih, Il, BPh, BPl) {
            var a = (Ih - Il);
            var b = (BPh - BPl);
            var c = (Cp - BPl);
            return Math.round((a / b) * c + Il);
        },

        getAQIDescription: function (aqi) {
            if (aqi >= 401) {
                return 'Hazardous';
            } else if (aqi >= 301) {
                return 'Hazardous';
            } else if (aqi >= 201) {
                return 'Very Unhealthy';
            } else if (aqi >= 151) {
                return 'Unhealthy';
            } else if (aqi >= 101) {
                return 'Unhealthy for Sensitive Groups';
            } else if (aqi >= 51) {
                return 'Moderate';
            } else if (aqi >= 0) {
                return 'Good';
            } else {
                return undefined;
            }
        },

        getAQIMessage: function (aqi) {
            if (aqi >= 401) {
                return '>401: Health alert: everyone may experience more serious health effects';
            } else if (aqi >= 301) {
                return '301-400: Health alert: everyone may experience more serious health effects';
            } else if (aqi >= 201) {
                return '201-300: Health warnings of emergency conditions. The entire population is more likely to be affected. ';
            } else if (aqi >= 151) {
                return '151-200: Everyone may begin to experience health effects; members of sensitive groups may experience more serious health effects.';
            } else if (aqi >= 101) {
                return '101-150: Members of sensitive groups may experience health effects. The general public is not likely to be affected.';
            } else if (aqi >= 51) {
                return '51-100: Air quality is acceptable; however, for some pollutants there may be a moderate health concern for a very small number of people who are unusually sensitive to air pollution.';
            } else if (aqi >= 0) {
                return '0-50: Air quality is considered satisfactory, and air pollution poses little or no risk';
            } else {
                return undefined;
            }
        }
    }

};

$(function () {
    d_aqi.init();
});

