<?php
/**
 * @copyright   Copyright (C) 2013 mktgexperts.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see http://www.gnu.org/licenses/gpl-2.0.html
 */



// no direct access
defined('_JEXEC') or die;

// load bootmark framework class
require_once(dirname(__FILE__) . '/lib/bootmark.php');
$bmf = new bootmarkFramework($this);
$app = Jfactory::getApplication();

?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php //<jdoc:include type="head" /> ?>
	<?php //$bmf->displayHead(); ?>
	<style type="text/css">
		header {background:#ddd;}
		#mainbody {background:#beeeff;}
		footer {background:#ddd;}

		img {max-width: 100%;}
		body {margin: 0;}

		.grid.container {margin: auto; background: rgba(0,0,0,0.1);}

		.grid {width: 960px;  }
		.grid > [class*="span-"] {float: left; background: rgba(0,0,0,0.1);}
		.grid > [class*="span-"] > .block {margin: 10px; padding: 15px; background: rgba(0,0,0,0.1);}

		.grid > .span-1 {width:8.3333333333%;}
		.grid > .span-2 {width:16.6666666667%;}
		.grid > .span-3 {width:25%;}
		.grid > .span-4 {width:33.3333333333%;}
		.grid > .span-5 {width:41.6666666667%;}
		.grid > .span-6 {width:50%;}
		.grid > .span-7 {width:58.3333333333%;}
		.grid > .span-8 {width:66.6666666667%;}
		.grid > .span-9 {width:75%;}
		.grid > .span-10 {width:83.3333333333%;}
		.grid > .span-11 {width:91.6666666667%;}
		.grid > .span-12 {width:100%;}

		@media only screen and (min-width: 960px) {
			.visible-desktop {display: inherit !important;}
			.hidden-desktop {display: none !important;}
			.visible-mobile {display: none !important;}
		}
		@media only screen and (max-width: 959px) {
			.visible-desktop {display: none !important;}
			.visible-mobile {display: inherit !important;}
			.hidden-mobile {display: none !important;}
		}

		.clear {background: none; border: 0; clear: both; display: block; float: none; font-size: 0; list-style: none; margin: 0; padding: 0; overflow: hidden; visibility: hidden; width: 0; height: 0;	}
	</style>
</head>
<body>
	<?php // begin header ?>
	<?php $group = "header" ; ?>
	<?php $max = $bmf->getGroupMaxLevel($group) ; ?>
	<?php if ($max && $bmf->hasModules("$group-[1-$max]-[a-f]")) : ?>
		<header>
			<?php for ($i=1; $i<=$max; $i++) : ?>
				<?php if ($bmf->hasModules("$group-$i-[a-f]")) : ?>
					<div id="<?php echo $group . "-" . $i?>">
						<div class="grid container responsive">
							<?php $bmf->displayModules($group, $i, "[a-f]"); ?>
							<div class="clear"></div>
						</div>
					</div>
				<?php endif; ?>
			<?php endfor; ?>
		</header>
	<?php endif; ?>
	<?php // end header ?>

	<?php // begin message block ?>
	<?php if ($app->getMessageQueue()) : ?>
	<div id="messageblock">
		<div class="grid container responsive">
			<div class="span-12">
				<div class="block">
					<jdoc:include type="message" />
				</div>
			</div>
			<div class="clear"></div>
		</div>
	</div>
	<?php endif; ?>
	<?php // end message block ?>

	<?php // begin before ?>
	<?php $group = "before" ; ?>
	<?php $max = $bmf->getGroupMaxLevel($group) ; ?>
	<?php if ($max && $bmf->hasModules("$group-[1-$max]-[a-f]")) : ?>
		<?php for ($i=1; $i<=$max; $i++) : ?>
			<?php if ($bmf->hasModules("$group-$i-[a-f]")) : ?>
				<div id="<?php echo $group . "-" . $i?>">
					<div class="grid container responsive">
						<?php $bmf->displayModules($group, $i, "[a-f]"); ?>
						<div class="clear"></div>
					</div>
				</div>
			<?php endif; ?>
		<?php endfor; ?>
	<?php endif; ?>
	<?php // end before ?>

	<?php // begin main body ?>
	<?php $group = "after" ; ?>
	<?php //if (check if mainbody and sibars has modules) : ?>
		<div id="mainbody">
			<div class="grid container responsive">
				<?php $bmf->displayMainBody(); ?>
				<div class="clear"></div>
			</div>
		</div>
	<?php //endif; ?>
	<?php // end main body ?>

	<?php // begin after ?>
	<?php $group = "after" ; ?>
	<?php $max = $bmf->getGroupMaxLevel($group) ; ?>
	<?php if ($max && $bmf->hasModules("$group-[1-$max]-[a-f]")) : ?>
		<?php for ($i=1; $i<=$max; $i++) : ?>
			<?php if ($bmf->hasModules("$group-$i-[a-f]")) : ?>
				<div id="<?php echo $group . "-" . $i?>">
					<div class="grid container responsive">
						<?php $bmf->displayModules($group, $i, "[a-f]"); ?>
						<div class="clear"></div>
					</div>
				</div>
			<?php endif; ?>
		<?php endfor; ?>
	<?php endif; ?>
	<?php // end after ?>

	<?php // begin footer ?>
	<?php $group = "footer" ; ?>
	<?php $max = $bmf->getGroupMaxLevel($group) ; ?>
	<?php if ($max && $bmf->hasModules("$group-[1-$max]-[a-f]")) : ?>
		<footer>
			<?php for ($i=1; $i<=$max; $i++) : ?>
				<?php if ($bmf->hasModules("$group-$i-[a-f]")) : ?>
					<div id="<?php echo $group . "-" . $i?>">
						<div class="grid container responsive">
							<?php $bmf->displayModules($group, $i, "[a-f]"); ?>
							<div class="clear"></div>
						</div>
					</div>
				<?php endif; ?>
			<?php endfor; ?>
		</footer>
	<?php endif; ?>
	<?php // end footer ?>

	<?php // begin hidden block ?>
	<?php if ($this->countModules('hiddenblock')) : ?>
		<div id="hiddenblock" style="display: none;">
			<jdoc:include type="hiddenblock" />
		</div>
	<?php endif; ?>
	<?php // end hidden block ?>

</body>
</html>
