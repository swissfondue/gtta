/*
 * Clears all checkboxes and scores from the dialog.
 */
function clearCVSScalc() {
    $("#baseMetricScore").text('');
    $("#baseSeverity").text('');
    $("#temporalMetricScore").text('');
    $("#temporalSeverity").text('');
    $("#environmentalMetricScore").text('');
    $("#environmentalSeverity").text('');
    $("#vectorString").hide();
    $("#cvss-dialog input").removeAttr('checked');
    $(".needBaseMetrics").show();

    $("#temporalMetricGroup_Legend").nextAll().hide();
    $("#environmentalMetricGroup_Legend").nextAll().hide();
}

/* ** updateScores **
 *
 * Updates Base, Temporal and Environmental Scores, and the Vector String (both in the web page and
 * in the fragment of the URL - the part after the "#").
 * If scores and vectors cannot be generated because the user has not yet selected values for all the Base Score
 * metrics, messages are displayed explaining this.
 */
function updateScores() {
    var result = CVSS.calculateCVSSFromMetrics(
        $("input:radio[name=AV]:checked").val(),
        $("input:radio[name=AC]:checked").val(),
        $("input:radio[name=PR]:checked").val(),
        $("input:radio[name=UI]:checked").val(),
        $("input:radio[name=S]:checked").val(),

        $("input:radio[name=C]:checked").val(),
        $("input:radio[name=I]:checked").val(),
        $("input:radio[name=A]:checked").val(),

        $("input:radio[name=E]:checked").val(),
        $("input:radio[name=RL]:checked").val(),
        $("input:radio[name=RC]:checked").val(),

        $("input:radio[name=CR]:checked").val(),
        $("input:radio[name=IR]:checked").val(),
        $("input:radio[name=AR]:checked").val(),
        $("input:radio[name=MAV]:checked").val(),
        $("input:radio[name=MAC]:checked").val(),
        $("input:radio[name=MPR]:checked").val(),
        $("input:radio[name=MUI]:checked").val(),
        $("input:radio[name=MS]:checked").val(),
        $("input:radio[name=MC]:checked").val(),
        $("input:radio[name=MI]:checked").val(),
        $("input:radio[name=MA]:checked").val());

    if (result.success === true) {
        // Hide text warning that scores, etc., cannot be calculated until user has selected a value for every Base Metric.
        $(".needBaseMetrics").hide();

        $("#baseMetricScore").text(result.baseMetricScore).parents('.scoreRating').attr('class', 'scoreRating ' + result.baseSeverity.toLowerCase());
        $("#baseSeverity").text("(" + result.baseSeverity + ")");

        $("#temporalMetricScore").text(result.temporalMetricScore).parents('.scoreRating').attr('class', 'scoreRating ' + result.temporalSeverity.toLowerCase());
        $("#temporalSeverity").text("(" + result.temporalSeverity + ")");

        $("#environmentalMetricScore").text(result.environmentalMetricScore).parents('.scoreRating').attr('class', 'scoreRating ' + result.environmentalSeverity.toLowerCase());
        $("#environmentalSeverity").text("(" + result.environmentalSeverity + ")");

        $("#vectorString").val(result.vectorString);
        $("#vectorString").show().css('display', 'inline-block');

    } else if (result.error === "Not all base metrics were given - cannot calculate scores.") {
        // Show text warning that scores, etc., cannot be calculated until user has selected a value for every Base Metric.
        $(".needBaseMetrics").show();
    }
}

/* ** setMetricsFromVector **
 *
 * Takes a Vector String and sets the metrics on the web page according to the values passed. The string passed
 * is fully validated, so it is okay to pass untrusted user input to this function. If validation fails, the
 * string "VectorMalformed" is returned and no changes are made to form field values.
 *
 * All base metrics must be specified. If they are not, the string "NotAllBaseMetricsProvided" is returned and no
 * changes are made to form field values. Temporal and Environmental metrics are optional and default to the value
 * "X" if not specified.
 *
 * If validation succeeds and all base metrics are provided, the form fields are set and "true" is returned.
 *
 * The standard prohibits a metric value being specified more than once, but this function does not prevent this
 * and uses the value of the last occurrence.
 */
function setMetricsFromVector(vectorString) {
    var result = true;
    var urlMetric, p;

    var metricValuesToSet = {
        AV: undefined,
        AC: undefined,
        PR: undefined,
        UI: undefined,
        S: undefined,
        C: undefined,
        I: undefined,
        A: undefined,
        E: "X",
        RL: "X",
        RC: "X",
        CR: "X",
        IR: "X",
        AR: "X",
        MAV: "X",
        MAC: "X",
        MPR: "X",
        MUI: "X",
        MS: "X",
        MC: "X",
        MI: "X",
        MA: "X"
    };

    // A regular expression to validate that a CVSS 3.0 vector string is well formed. It checks metrics and metric values, but
    // does not check that all base metrics have been supplied. That check is done later.
    var vectorStringRegex_30 = /^CVSS:3.0\/((AV:[NALP]|AC:[LH]|PR:[UNLH]|UI:[NR]|S:[UC]|[CIA]:[NLH]|E:[XUPFH]|RL:[XOTWU]|RC:[XURC]|[CIA]R:[XLMH]|MAV:[XNALP]|MAC:[XLH]|MPR:[XUNLH]|MUI:[XNR]|MS:[XUC]|M[CIA]:[XNLH])\/)*(AV:[NALP]|AC:[LH]|PR:[UNLH]|UI:[NR]|S:[UC]|[CIA]:[NLH]|E:[XUPFH]|RL:[XOTWU]|RC:[XURC]|[CIA]R:[XLMH]|MAV:[XNALP]|MAC:[XLH]|MPR:[XUNLH]|MUI:[XNR]|MS:[XUC]|M[CIA]:[XNLH])$/;

    if (vectorStringRegex_30.test(vectorString)) {
        var urlMetrics = vectorString.substring("CVSS:3.0/".length).split("/");

        for (p in urlMetrics) {
            urlMetric = urlMetrics[p].split(":");
            metricValuesToSet[urlMetric[0]] = urlMetric[1];
        }

        // Only if *all* base metrics have been provided, directly set form fields to the required values.
        if (metricValuesToSet.AV !== undefined &&
            metricValuesToSet.AC !== undefined &&
            metricValuesToSet.PR !== undefined &&
            metricValuesToSet.UI !== undefined &&
            metricValuesToSet.S !== undefined &&
            metricValuesToSet.C !== undefined &&
            metricValuesToSet.I !== undefined &&
            metricValuesToSet.A !== undefined) {

            // The correct form field to set can be worked out from the metric acronym and value due to the naming
            // convention used on the web page. For example, setting Access Vector (AV) to Physical (P) requires
            // the form field with the id "AV_P" to be checked.

            for (p in metricValuesToSet) {
                document.getElementById(p + "_" + metricValuesToSet[p]).checked = true;
            }
        } else {
            result = "NotAllBaseMetricsProvided";
        }

    } else {
        result = "MalformedVectorString";
    }

    // Field values have been set directly, rather than by the user clicking form fields, so the triggers to
    // recalculate scores have not fired. Therefore, explicitly update the scores now.

    updateScores();

    return result;
}


// Used to store the current CVSS Vector from the URL so that we can detect if the user changes it.
var CVSSVectorInURL;

$(document).ready(function () {
    // Update the CVSS scores and Vector String whenever an input field is clicked
    $("input").bind("click", function () {
        updateScores();
    });

    // Add a handler to toggle the display of everything in a metric group when the metric group title is clicked.
    $("fieldset legend").bind("click", function (event) {
        $(event.target).nextAll().slideToggle();
    });

    $("#temporalMetricGroup_Legend").nextAll().hide();
    $("#environmentalMetricGroup_Legend").nextAll().hide();

    // Add titles to every metric element containing help text. This is displayed when the user hovers over the
    // element.
    $.each(CVSS_Help.helpText_en, function (helpID, helpText) {
        $("#" + helpID).attr("title", helpText);
    });

    /* Create anonymous functions that are called when the Vector String displayed on the page is clicked. Both
     * select the entire Vector String to make it quicker to copy to the operating system's clipboard.
     */
    $("#vectorString")
        .bind("click", function () {
            $("#vectorString").select();
        })
        .bind("contextmenu", function () {
            $("#vectorString").select();
        });
});
