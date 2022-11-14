$(function () {

  'use strict'

  var ticksStyle = {
    fontColor: '#495057',
    fontStyle: 'bold'
  }

  var mode = 'index'
  var intersect = true


  var chartColors = {
    red: 'rgb(255, 99, 132)',
    orange: 'rgb(255, 159, 64)',
    yellow: 'rgb(255, 205, 86)',
    green: 'rgb(26, 255, 26)',
    blue: 'rgb(54, 162, 235)',
    purple: 'rgb(153, 102, 255)',
    grey: 'rgb(201, 203, 207)',
    chocolate: 'rgb(210, 105, 30)',
    darkSlateGray: 'rgb(47, 79, 79)',
    indigo: 'rgb(75, 0, 130)',
    lightCoral: 'rgb(240, 128, 128)',
    sienna: 'rgb(160, 82, 45)',
    turquoise: 'rgb(64, 224, 208)',
    fuchsia: 'rgb(255, 0, 255)',
    lavender: 'rgb(230, 230, 250)',
    brown: 'rgb(165, 42, 42)',
    white: 'rgb(255, 255, 255)',
    lightSeaGreen: 'rgb(32, 178, 170)',
    maroon: 'rgb(128, 0, 0)',
    midnightBlue: 'rgb(25, 25, 112)',
    moccasin: 'rgb(255, 228, 181)',
    mediumVioletRed: 'rgb(199, 21, 133)',
    olive: 'rgb(128, 128, 0)',
    orangeRed: 'rgb(255, 69, 0)',
    paleVioletRed: 'rgb(219, 112, 147)',
    rosyBrown: 'rgb(188, 143, 143)',
    saddleBrown: 'rgb(139, 69, 19)',
    salmon: 'rgb(250, 128, 114)',
    brownreddish: 'rgb(121, 102, 0)',
    wheat: 'rgb(245,222,179)'
  };

  // index color array
  var indexedChartColor = []

  for (var color in chartColors) {
    indexedChartColor.push(chartColors[color])
  }

  /**
   * students per class chart
   * @type  function
   */
  let num_of_students_per_class_chart = function () {

    var jqxhr = $.ajax({
      url: "ajax.php?action=num_of_students_per_class_chart",
      method: "POST",
      dataType: "json"
    }).done(function (response) {

      var numstudent = [],
        classname = []

      for (var i in response) {
        numstudent.push(response[i].numstudent);
        classname.push(response[i].classname);
      }

      var $studPerClassChart = $('#students-per-class-chart')
      var studPerClassChart = new Chart($studPerClassChart, {
        type: 'bar',
        data: {
          labels: classname,
          datasets: [
            {
              backgroundColor: indexedChartColor,
              borderColor: indexedChartColor,
              data: numstudent
            }
          ]
        },
        options: {
          maintainAspectRatio: false,
          tooltips: {
            mode: mode,
            intersect: intersect
          },
          hover: {
            mode: mode,
            intersect: intersect
          },
          legend: {
            display: false
          },
          scales: {
            yAxes: [{
              display: true,
              gridLines: {
                display: true,
                // lineWidth: '4px',
                // color: 'rgba(0, 0, 0, .8)',
                zeroLineColor: 'transparent'
              },
              ticks: $.extend({
                beginAtZero: true,
                suggestedMax: 15
              }, ticksStyle)
            }],
            xAxes: [{
              display: true,
              gridLines: {
                display: true,
                // lineWidth: '4px',
                // color: 'rgba(0, 0, 0, .8)',
                zeroLineColor: 'transparent'
              },
              ticks: ticksStyle
            }]
          }
        }
      }); // chart config

    }).fail(function (jqXHR) {
      console.error(jqXHR)
    })
  }
  num_of_students_per_class_chart()


  /**
   * payments being made chart
   */
  let paymentsmade = function () {

    var jqxhr = $.ajax({
      url: "ajax.php?action=daily_fee_payment_chart",
      method: "POST",
      dataType: "json"
    }).done(function (response) {

      var amt = [],
        pdate = []

      for (var i in response) {
        amt.push(response[i].amt);
        pdate.push(response[i].pdate);
      }


      var $dailyFeePaymentChart = $('#daily-payment-chart')
      var dailyFeePaymentChart = new Chart($dailyFeePaymentChart, {
        data: {
          labels: pdate,
          datasets: [{
            type: 'line',
            data: amt,
            backgroundColor: 'transparent',
            borderColor: indexedChartColor,
            pointBorderColor: indexedChartColor,
            pointBackgroundColor: indexedChartColor,
            fill: false
            // pointHoverBackgroundColor: '#007bff',
            // pointHoverBorderColor    : '#007bff'
          }]
        },
        options: {
          maintainAspectRatio: false,
          tooltips: {
            mode: mode,
            intersect: intersect
          },
          hover: {
            mode: mode,
            intersect: intersect
          },
          legend: {
            display: false
          },
          scales: {
            yAxes: [{
              // display: false,
              gridLines: {
                display: true,
                lineWidth: '4px',
                color: 'rgba(0, 0, 0, .2)',
                zeroLineColor: 'transparent'
              },
              ticks: $.extend({
                beginAtZero: true,
                suggestedMax: 200
              }, ticksStyle)
            }],
            xAxes: [{
              display: true,
              gridLines: {
                display: false
              },
              ticks: ticksStyle
            }]
          }
        }
      })

    }).fail(function (jqXHR) {
      console.error(jqXHR)
    })

  }
  paymentsmade()



}); // jquery


// ----------
//  calendar
// ----------
$(function () {


  $('#calendar').datetimepicker({
    format: 'L',
    inline: true
  })
});
