jQuery(document).ready(function ($) {


    $('.player-link-popup').each(function () {
        var $el = $(this)

        $el.tooltipster({
            content: $('<a href="'+ $el.attr('href') +'" class="player-quick-box tooltip-box no-image"><strong>' + $el.text() + '</strong><br>' + $el.data('position') +  ($el.data('club_team') == '' ? '' : ' | ' + $el.data('club_team') ) + ($el.data('recruiting_school') == 'Uncommitted' || $el.data('recruiting_school') == '' ? '' : '<br>Committed to: ' + $el.data('recruiting_school'))
                + '<br>Ranking: ' + createStarRating($el.data('rating')) + '</a>'),
            animation: 'fade',
            delay: 200,
            theme: 'tooltipster-default',
            trigger: 'hover',
            interactive: true,
            autoClose: false,
            minWidth: 350,

        })
    })

    function createStarRating(rating) {

        if (rating === 'locked') {
            return '<i aria-hidden="true" class="fa fa-lock" style="color:#fff;"></i>'
        }

        let starRating = ''
        for (let i = 0; i < 5; i++) {
            if (i < rating) {
                starRating += '<i class="elementor-star-full">★</i>'
            } else {
                starRating += '<i class="elementor-star-empty">☆</i>'
            }
        }
        return starRating
    }


    $('.select_ua_next_camp').on('click', function (e) {
        e.preventDefault()
        let camp = $(this).data('select')
        $('select[data-tax=ua_next_camp]').val(camp).change()
    })


    $(document).on("click", ".MultiCheckBox", function () {
        var detail = $(this).next();
        detail.show();
    });

    $(document).on("click", ".MultiCheckBoxDetailHeader input", function (e) {
        e.stopPropagation();
        var hc = $(this).prop("checked");
        $(this).closest(".MultiCheckBoxDetail").find(".MultiCheckBoxDetailBody input").prop("checked", hc);
        $(this).closest(".MultiCheckBoxDetail").next().UpdateSelect();
    });

    $(document).on("click", ".MultiCheckBoxDetailHeader", function (e) {
        var inp = $(this).find("input");
        var chk = inp.prop("checked");
        inp.prop("checked", !chk);
        $(this).closest(".MultiCheckBoxDetail").find(".MultiCheckBoxDetailBody input").prop("checked", !chk);
        $(this).closest(".MultiCheckBoxDetail").next().UpdateSelect();
    });

    $(document).on("click", ".MultiCheckBoxDetail .cont input", function (e) {
        e.stopPropagation();
        $(this).closest(".MultiCheckBoxDetail").next().UpdateSelect();

        var val = ($(".MultiCheckBoxDetailBody input:checked").length == $(".MultiCheckBoxDetailBody input").length)
        $(".MultiCheckBoxDetailHeader input").prop("checked", val);
    });

    $(document).on("click", ".MultiCheckBoxDetail .cont", function (e) {
        var inp = $(this).find("input");
        var chk = inp.prop("checked");
        inp.prop("checked", !chk);

        var multiCheckBoxDetail = $(this).closest(".MultiCheckBoxDetail");
        var multiCheckBoxDetailBody = $(this).closest(".MultiCheckBoxDetailBody");
        multiCheckBoxDetail.next().UpdateSelect();

        var val = ($(".MultiCheckBoxDetailBody input:checked").length == $(".MultiCheckBoxDetailBody input").length)
        $(".MultiCheckBoxDetailHeader input").prop("checked", val);
    });

    $(document).mouseup(function (e) {
        var container = $(".MultiCheckBoxDetail");
        if (!container.is(e.target) && container.has(e.target).length === 0) {
            container.hide();
        }
    });


})


var defaultMultiCheckBoxOption = {width: '220px', defaultText: 'Select Below', height: '200px'};


jQuery.fn.extend({
    CreateMultiCheckBox: function (options) {

        var localOption = {};
        localOption.width = (options != null && options.width != null && options.width != undefined) ? options.width : defaultMultiCheckBoxOption.width;
        localOption.defaultText = (options != null && options.defaultText != null && options.defaultText != undefined) ? options.defaultText : defaultMultiCheckBoxOption.defaultText;
        localOption.height = (options != null && options.height != null && options.height != undefined) ? options.height : defaultMultiCheckBoxOption.height;

        this.hide();
        this.attr("multiple", "multiple");
        var divSel = jQuery("<div class='MultiCheckBox'>" + localOption.defaultText + "<span class='k-icon k-i-arrow-60-down'><svg aria-hidden='true' focusable='false' data-prefix='fas' data-icon='sort-down' role='img' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 320 512' class='svg-inline--fa fa-sort-down fa-w-10 fa-2x'><path fill='currentColor' d='M41 288h238c21.4 0 32.1 25.9 17 41L177 448c-9.4 9.4-24.6 9.4-33.9 0L24 329c-15.1-15.1-4.4-41 17-41z' class=''></path></svg></span></div>").insertBefore(this);
        divSel.css({"width": localOption.width});

        var detail = jQuery("<div class='MultiCheckBoxDetail'><div class='MultiCheckBoxDetailHeader'><input type='checkbox' checked class='mulinput' value='-1982' /><div>Select All</div></div><div class='MultiCheckBoxDetailBody'></div></div>").insertAfter(divSel);
        detail.css({"width": parseInt(options.width) + 10, "max-height": localOption.height});
        var multiCheckBoxDetailBody = detail.find(".MultiCheckBoxDetailBody");

        this.find("option").each(function () {
            var val = jQuery(this).attr("value");

            if (val == undefined) val = '';

            multiCheckBoxDetailBody.append("<div class='cont'><div><input type='checkbox' class='mulinput' checked value='" + val + "' /></div><div>" + jQuery(this).text() + "</div></div>");
        });

        multiCheckBoxDetailBody.css("max-height", (parseInt(jQuery(".MultiCheckBoxDetail").css("max-height")) - 28) + "px");
    }, UpdateSelect: function () {
        var arr = [];

        this.prev().find(".mulinput:checked").each(function () {
            arr.push(jQuery(this).val());
        });

        this.val(arr).trigger("change");
    },
})


jQuery('.player-search-filters select').on('change', function () {
    if (jQuery.fn.DataTable.isDataTable('.posts-data-table')) {
        let table = jQuery('.posts-data-table').DataTable()
        table.draw()
    }
})


// jquery after everything is loaded
jQuery(document).ready(function () {

    jQuery('#graduating_class').CreateMultiCheckBox({
        width: '230px', defaultText: 'Graduating Class', height: '250px'
    })
    jQuery('#position').CreateMultiCheckBox({
        width: '230px', defaultText: 'Position', height: '250px'
    })
    jQuery('#recruiting_school').CreateMultiCheckBox({
        width: '230px', defaultText: 'College Commitment', height: '250px'
    })


    // select all checkboxes by default
    jQuery('.dropdown-check-list').each(function () {
        jQuery(this).prop('checked', true)
    })


    jQuery('.player-search-filters select').trigger('change')


    jQuery(".MultiCheckBoxDetail").each(function () {
        jQuery(this).next().UpdateSelect()
    })

})




