<script language="JavaScript" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

class Strt {
    constructor ( ) {
        this.initWidgets();

        this.url = '../install/startupSrvr.php';
        this.dest = './index.php';

        $('#constBtn').on('click',null, $.proxy(this.doCreateConstFile, this));
        $('#newDbBtn').on('click',null, $.proxy(this.createDb, this));
        $('#skipBtn').on('click',null,function() {
            $('#createDB').hide();
            $('#continue').show();
        });
        $('#contBtn').on('click',null, $.proxy(this.doContinue, this));
        $('#restartBtn').on('click',null, $.proxy(this.resetForms, this));
        $( document ).ajaxError(function() { $( ".log" ).text( "Triggered ajax Error handler." ); });

        this.resetForms();
//        $('#dbEngine').on('change',null, function () {
//            console.log($('#dbEngine').val());
//        });
    };

    //------------------------------
    initWidgets () {
    };
    resetForms () {
        //console.log('resetting!');
        $('#progress').hide();
        $('#plsWait').hide();
        $('#const_editor').show();
        $('#createDB').hide();
        $('#continue').hide();
        $('#hostId').focus();
        this.informUser('Creating new database constant file');
    };
    informUser (msg) {
        var html = '<li>'+msg+'</li>';
        $('#progressList').append(html);
        $('#progress').show();
    };
    showWait (msg) {
        $('#waitMsg').html(msg);
        $('#plsWait').show();
    };

    //------------------------------
    doCreateConstFile (e) {
        e.preventDefault();
        e.stopPropagation();

        this.showWait('Creating file');

        this.dbEngine = $('#dbEngine').val();
        this.host = $('#hostId').val();
        this.user = $('#userNm').val().replace(/\+/g, '%2b');
        this.pw = $('#passWd').val().replace(/\+/g, '%2b');
        this.db = $('#dbName').val().replace(/\+/g, '%2b');
        var params = "mode=createConstFile&host="+this.host+"&dbEngine="+this.dbEngine+"&user="+this.user+"&passwd="+this.pw+"&db="+this.db;
        var _this = this;

        $.post(this.url, params, function (response) {
            $('#plsWait').hide();
            if (response.hasOwnProperty('error')) {
              $.each( response, function( key, val ) { _this.informUser(val); });
            } else  {
              _this.informUser('A new database_constant file has been created');
              _this.informUser('Collecting access parameters');
              $('#const_editor').hide();
              $('#createDB').show();
              $('#adminNm').focus();
            }
        }, 'json');
    };

    createDb (e) {
        e.preventDefault();
        e.stopPropagation();

        this.showWait('Creating database');

        var adminNm = $('#adminNm').val();
        var adminPw = $('#adminPw').val();
        var params = "mode=createDatabase"+
                     "&host="+this.host+
                     "&user="+this.user+
                     "&passwd="+this.pw+
                     "&db="+this.db+
                     "&adminNm="+adminNm+
                     "&adminPw="+adminPw;
        var _this = this;

        $.post(this.url, params, function (response) {
            $('#plsWait').hide();
            if (response.indexOf('PDOException:') >= 0) {
              _this.informUser('<p class="error">'+response+'</p>');
            } else if (response.indexOf('success') >= 0) {
              _this.informUser('A new EMPTY database has been created');
              $('#createDB').hide();
              $('#continue').show();
            }
        });
    };

    doContinue () {
console.log('Creating database');
        this.showWait('Creating database');
console.log('Transferring to database install/create');
        this.informUser('Transferring to database install/create');
console.log('new dest is: '+this.dest);
        window.location.href = this.dest;
console.log('shouldnt get here');
		return false;
    };
}

$(document).ready(function () {
	var strt = new Strt();
});
</script>
