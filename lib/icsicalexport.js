
'use strict';
jQuery(document).ready(function(){
	
    var cal = '<?php echo $pluginRootURL ?>/fetcher.php?c=<?php echo $c ?>&hmd=<?php echo $hmd ?>&mr=<?php echo $mr ?>';	
    var entries = null;
    var entry = null;
    var webcalLink = null;
    var dayDisplayFormat = 'dddd, MMM. dS';
    var reMilliseconds = /\.\d+/g;
    var timeDisplayFormat = 'h:mmtt';
    var currentDay = null;

    var handleSuccess = function(xml) {
        var Math_floor = Math.floor;
        var Date_parse = Date.parse;
        
        webcalLink = jQuery(xml).find('feed link[rel=alternate]').attr('href');
        entries = jQuery(xml).find('entry');
                    
        var docFrag = document.createDocumentFragment();
        var theDL = document.createElement('dl');
        var aDTforDay = document.createElement('dt');
        var aDDforTitle = document.createElement('dd');
        var anEventLink = document.createElement('a');
        var aDDforTime = document.createElement('dd');
        
        for (var i=0,len=entries.length; i < len; i++) {
            entry = jQuery(entries[i]);
            title = entry.find('title').text();

            startTime = entry.find('[startTime]').attr('startTime');
            startTime = startTime.replace(reMilliseconds, '');

            endTime = entry.find('[endTime]').attr('endTime');
            endTime = endTime.replace(reMilliseconds, '');

            entry.startTime = new XDate(startTime);
            entry.endTime = new XDate(endTime);

            durationInTotalMinutes = entry.startTime.diffMinutes(entry.endTime);

            durationHours = Math_floor(durationInTotalMinutes/60);
            durationMinutes = ('0'+ (durationInTotalMinutes % 60)).substr(-2,2);
            entry.duration = { hours: durationHours, minutes: durationMinutes };

            st_day = entry.startTime.toString(dayDisplayFormat);
            st_time = entry.startTime.toString(timeDisplayFormat);
            et_day = entry.endTime.toString(dayDisplayFormat);
            et_time = entry.endTime.toString(timeDisplayFormat);
            
            link = entry.find('link[rel=alternate]').attr('href');
            
            if (currentDay !== st_day) {
                currentDay = st_day;
                var thisDTforDay = aDTforDay.cloneNode(false);
                thisDTforDay.innerHTML = st_day;
                theDL.appendChild(thisDTforDay);
            }
            var thisDDforTitle = aDDforTitle.cloneNode(false);
            anEventLink.href = link + '&ctz=<?php echo $tz ?>';
            anEventLink.innerHTML = title;					
            thisDDforTitle.appendChild(anEventLink.cloneNode(true));
            theDL.appendChild(thisDDforTitle);

            var thisDDforTime = aDDforTime.cloneNode(false);
            thisDDforTime.className = 'time';
            thisDDforTime.innerHTML = st_time.substr(0,st_time.length-1).toLowerCase() + '-' + et_time.substr(0,et_time.length-1).toLowerCase();					
            theDL.appendChild(thisDDforTime);

        }
        
        var theClosingParagraph = document.createElement('p');
        var theCalendarLink = document.createElement('a');
        theCalendarLink.href = webcalLink + '&ctz=<?php echo $tz ?>';
        theCalendarLink.innerHTML = 'Whole calendar';
        theClosingParagraph.appendChild(theCalendarLink);

        docFrag.appendChild(theDL);
        docFrag.appendChild(theClosingParagraph);

        var theParentDiv = jQuery('#wp-gcal-rss_dates');
        theParentDiv[0].appendChild(docFrag);
        
        jQuery('#wp-gcal-rss_loading').hide();
        theParentDiv.slideDown();
    };
    
    var handleError = function(x, t, e) {
        jQuery('#wp-gcal-rss_loading').hide();
        jQuery('#wp-gcal-rss_error').slideDown();
    };
    
    jQuery.ajax({
        url: cal,
        dataType: 'xml',
        success: handleSuccess,
        error: handleError
    });


});
