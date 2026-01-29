<?php
	require_once("../shared/common.php");
	require_once(REL(__FILE__,"../functions/inputFuncs.php"));
	require_once(REL(__FILE__, "../model/Sites.php"));

	setSessionFmSettings();  ## found in ../shared/common.php
	
	$temp_return_page = "";
	if (isset($_GET["RET"])){
		$_SESSION["returnPage"] = $_GET["RET"];
	}

	$sites_table = new Sites;
	$sites = $sites_table->getSelect();

	if(isset($_REQUEST['selectSite'])){
		$siteId = $_REQUEST['selectSite'];
	} elseif(isset($_COOKIE['OpenBiblioSiteID'])) {
		$site = new Sites;
		$exists_in_db = $site->maybeGetOne($_COOKIE['OpenBiblioSiteID']);
		if ($exists_in_db['siteid'] != $_COOKIE['OpenBiblioSiteID']) {
			$_COOKIE['OpenBiblioSiteID'] = 1;
		}
		$siteId = $_COOKIE['OpenBiblioSiteID'];
	} else {
		$siteId = Settings::get('multi_site_func');
		if(!($siteId > 0)){
			$siteId = 1;
		}
		$_REQUEST['selectSite'] = $siteId;
	}
	$tab = "circ";
	$nav = "";
	$focus_form_name = "loginform";
	$focus_form_field = "username";

	require_once(REL(__FILE__, "../shared/get_form_vars.php"));
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

?>
<style>
/* ---- RESET ---- */
* {
    box-sizing: border-box;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI",
                 Roboto, Helvetica, Arial, sans-serif;
}

/* ---- BACKGROUND ---- */
body {
    margin: 0;
    min-height: 100vh;
    background: linear-gradient(135deg, #057022, #034415);
    display: flex;
    align-items: center;
    justify-content: center;
}

/* ---- LOGIN CARD ---- */
.login-container {
    width: 100%;
    max-width: 380px;
    padding: 2.5rem 2rem;
    background: #eee;
    
		border-top-right-radius: 30px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
    text-align: center;

    position: relative; /* 👈 important */
}


/* ---- LOGO ---- */
.login-logo img {
    max-width: 150px;
    height: auto;
    margin-bottom: 1rem;
}

.login-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 1.5rem;
}

/* ---- FORM ---- */
.login-form {
    display: flex;
    flex-direction: column;
    gap: 0.9rem;
}

.login-form select, #password, #username {
    padding: 0.65rem 0.75rem;
    font-size: 0.95rem;
    border: 1px solid #ccc;
    border-radius: 6px;
    background: #fafafa;
    transition: border 0.2s, box-shadow 0.2s;
}

.mylink {
	  font-size: 0.95rem;
		color: #444;
}

.login-form input:focus,
.login-form select:focus {
    outline: none;
    border-color: #0082c9;
    box-shadow: 0 0 0 2px rgba(0, 130, 201, 0.15);
    background: #fff;
}

/* ---- BUTTON ---- */
#login {
    width: 100%;
    padding: 0.7rem;
    font-size: 0.95rem;
    font-weight: 600;
    color: #fff;
    background: #079C2F;
    border: none;
    border-radius: 6px;
		
    cursor: pointer;
    transition: background 0.2s, opacity 0.2s;
}

#login:hover:not(:disabled) {
    background: #09C83C;
}

#login:disabled {
    background: #8bafcc;
    cursor: not-allowed;
    opacity: 0.8;
}

.login-form button:hover {
    background: #057022;
}

/* ---- FOOTER ---- */
.login-footer {
    margin-top: 1.5rem;
    font-size: 0.8rem;
    color: #666;
}

.login-footer a {
	 text-decoration: none;
}

/* ---- MOBILE ---- */


/* Login page layout override */
@media screen and (min-width: 800px) {
  main#content {
    position: static;
    left: auto;
    top: auto;
    width: auto;
    padding: 0;
		
		background: transparent;
		border: none;
  }
}

/* Login page layout override */
@media screen and (max-width: 799px) {
  main#content {
		border-top-right-radius: 30px;
    position: static;
    left: auto;
    top: auto;
    width: auto;
    padding: 0;
		
		background: transparent;
		border: none;
  }
}

#sidebar {
	display: none;
}

.opac-text {
	display: none;
}
/* ---- OPAC BUTTON (INSIDE CARD) ---- */
.opac-btn {
    position: absolute;
    top: 8px;
    right: 8px;

    width: 56px;
    height: 56px;

    border-radius: 50%;
    background: transparent;
    color: #fff;

    display: flex;
    align-items: center;
    justify-content: center;

    font-size: 1.45rem;
    font-weight: 700;
    letter-spacing: 0.05em;
		padding-bottom: 2px;
    text-decoration: none;

    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.25);
    transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s;
}

.opac-btn:hover {
    /* background: #09C83C; */
		background: #079C2F;
    transform: translateY(-1px);
    box-shadow: 0 6px 9px rgba(0, 0, 0, 0.3);
		.opac-text {
			display: flex;
			font-size: 0.9rem;
		}
		
		.opac-book {
			display: none;
		}
}

.opac-btn:active {
    transform: translateY(0);
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.25);
}
</style>
<section class="login-container">
		<a href="../catalog/srchForms.php?tab=OPAC" class="opac-btn"><span class="opac-book">📚</span><span class="opac-text">OPAC</span></a>
		
		<div class="login-logo">
        <!-- Placeholder logo -->
        
				<a href="https://github.com/dtr-kalfer" target="_blank"><img src="../images/burauenbiblio2.webp" alt="BurauenBiblio Logo"></a>
    </div>

    <div class="login-title">
        Welcome to BurauenBiblio
    </div>

    <form class="login-form" role="form" name="loginform" method="post" action="../shared/login.php">
        <input
					id="username" 
					name="username" 
					type="text"
					placeholder="Username"
					required
        >

        <input
					id="password"
					name="pwd"
					type="password"
					placeholder="Password"
					required
        >

		
<label id="showpass_label"><input type="checkbox" id="showPassword" /> show password</label>


	<?php if(($_SESSION['multi_site_func'] > 0) || ($_SESSION['site_login'] == 'Y')){ ?>
		<label id="librarysite" for="selectSite"></label>
			<?php echo inputfield('select', 'selectSite', $siteId, NULL, $sites) ?>	
	<?php } ?>
    <button id="login" type="submit"><?php echo T("Login"); ?></button>
				
				
				
    </form>

    <div class="login-footer">
        <a href="../COPYRIGHT.html" class="mylink" target="_blank">© <?php echo date('Y'); ?> Ferdinand Tumulak</a>
    </div>
</section>


<p id="login-error" style="text-align: center;" class="error">
	<?php 
		echo $_SESSION["pageErrors"]["pwd"] ?? '';
	?>
</p>
<script>
"use strict";

document.addEventListener("DOMContentLoaded", function () {

    const password = document.getElementById('password');
    const showPassword = document.getElementById('showPassword');
    const errorMsg = document.getElementById("login-error");
    const loginBtn = document.getElementById("login");

    if (showPassword && password) {
        showPassword.addEventListener('change', function () {
            password.type = this.checked ? 'text' : 'password';
        });
    }

    if (errorMsg && loginBtn &&
        errorMsg.textContent.trim().includes("Invalid username or password")) {

        let countdown = 10;
        loginBtn.disabled = true;
        loginBtn.textContent = `Please wait ${countdown} seconds...`;

        const interval = setInterval(() => {
            countdown--;

            if (countdown > 0) {
                loginBtn.textContent = `Please wait ${countdown} seconds...`;
            } else {
                clearInterval(interval);
                loginBtn.disabled = false;
                loginBtn.textContent = "Login";
                errorMsg.textContent = "";
            }
        }, 1000);
    }
});
</script>
<?php
  require_once(REL(__FILE__,'../shared/footer.php'));
?>	
