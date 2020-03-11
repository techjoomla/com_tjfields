<?php
/**
 * @package	TJ-Fields
 * @author	 TechJoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2019 TechJoomla. All rights reserved.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

if (!key_exists('field', $displayData) || !key_exists('fieldXml', $displayData))
{
	return;
}

$xmlField = $displayData['fieldXml'];
$field = $displayData['field'];

?>
<fieldset id="<?php echo $field->id; ?>" class="rating nohover <?php echo $field->ratingstyle;?> ">
<?php
for ($rating = $field->maxrating; $rating >= $field->minrating; $rating--)
{
	$checked = '';

	if ($rating == $field->value)
	{
		$checked = 'checked="checked"';
	}
?>
	<input type="radio" id="<?php echo $field->id . $rating; ?>" name="<?php echo $field->name; ?>" value="<?php echo $rating; ?>" <?php echo $checked; ?> disabled="disabled" /><label class = "full" for="<?php echo $field->id . $rating; ?>" title="<?php echo $rating . $field->ratingstyle; ?>"></label>
<?php
	if ($field->ratingstep)
	{
		$halfRating = ($field->ratingstep) / (2);
		$ratingValue = $rating - $halfRating;

		$checked = '';

		if ($ratingValue == $field->value)
		{
			$checked = 'checked="checked"';
		}?>

		<input type="radio" id="<?php echo $field->id . $ratingValue; ?>" name="<?php echo $field->name; ?>" value="<?php echo $ratingValue; ?>
		" <?php echo $checked; ?> disabled="disabled" /><label class = "half" for="<?php echo $field->id . $ratingValue; ?>" title="<?php echo $ratingValue . $field->ratingstyle; ?>"></label>
		<?php
	}
	$rating = $rating - ($field->ratingstep - 1);
} ?>
</fieldset>

