
<h1>Trouvez nos entrepreneurs</h1>

<form action="<?=get_permalink() ?>" method="get">
<table id="spl-search">
	<tr>
		<td><label for="code postal">Code postal</label></td>
		<td><label for="ville">Ville</label></td>
		<td><label for="departement">Département</label></td>
		<td><label for="activite">Secteur activité</label></td>
		<td>
			<?php if( is_user_logged_in() or true ): ?>
			<label for="hebergement">Hébergement</label>
			<input type="checkbox" name="hebergement" id='hebergement'/>
			<label for="covoit">Covoiturage</label>
			<input type="checkbox" name="covoit" id='covoit'/>
			<?php endif; ?>
		</td>
	</tr>
	<tr>
		<td>
			<input type="text" name="codepostal" 
				value="<?=isset($_REQUEST['codepostal'])?$_REQUEST['codepostal']:''?>">
		</td>
		<td>
			<input type="text" name="ville" 
				value="<?=isset($_REQUEST['ville'])?$_REQUEST['ville']:''?>">
		</td>
		<td>
			<?php
				$dept = isset($_REQUEST['departement']) ? $_REQUEST['departement'] : '';
			?>
			<select name="departement">
				<option></option>
				<option value="31" <?=$dept=='31'?'selected':''?>>31 - Haute-Garonne</option>
				<option value="32" <?=$dept=='32'?'selected':''?>>32 - Gers</option>
				<option value="40" <?=$dept=='40'?'selected':''?>>40 - Landes</option>
				<option value="64" <?=$dept=='64'?'selected':''?>>64 - Pyrénées-Atlantiques</option>
				<option value="65" <?=$dept=='65'?'selected':''?>>65 - Hautes-Pyrénées</option>
				<option value="82" <?=$dept=='82'?'selected':''?>>82 - Tarn-et-Garonne</option>
			</select>
		</td>
		<td>
			<?php
				$act = isset($_REQUEST['activite']) ? $_REQUEST['activite'] : '';
			?>
			<select name="activite">
				<option></option>
				<?php foreach( $joblist as $j ){
					echo '<option' . ($act==$j?' selected':'') . '>' . $j . '</option>';

				} ?>
			</select>
		</td>
		<td>
			<input type="hidden" name="page_id" value='1677'/>
			<button type="submit">voir</button>
		</td>
	</tr>
</table>
</form>







<div id='map'></div>

<script type='text/javascript'>
InitialiserCarte( <?=$listaddressjson?> );
</script>





<table id="spl-user">
	<thead>
		<tr>
			<th>&nbsp;</th>
			<th>Nom</th>
			<th>Prénom</th>
			<th>Tél.</th>
			<th>Ville</th>
			<th>Dept</th>
			<th>CP</th>
			<th>activité(s)</th>
			<th>Hébergement</th>
			<th>Covoiturage</th>
		</tr>
	</thead>
	<tbody>
		<?php

		function dept($code){
			$dept = [
				31 => 'Haute-Garonne',
				32 => 'Gers',
				40 => 'Landes',
				64 => 'Pyrénées-Atlantiques',
				65 => 'Hautes-Pyrénées',
				82 => 'Tarn-et-Garonne'
			];
			return isset($dept[$code]) ? $dept[$code] : '';
		}

		foreach( $db as $u ):

			if(
				   !isset($u['user_ville'])
				|| trim($u['user_ville'])==''
				|| !isset($u['user_codepostal'])
				|| trim($u['user_codepostal'])==''
				|| !isset($u['lat'])
				|| !isset($u['lon'])
			){
				continue;
			}


			?>
			<tr>
				<td><?= isset($u['profilepicture'])?'<img src="'.$u['profilepicture'].'"/>':'' ?></td>
				<td><?= isset($u['first_name'])?$u['first_name']:'' ?></td>
				<td><?= isset($u['last_name'])?$u['last_name']:'' ?></td>
				<td><?= isset($u['phone_number'])?$u['phone_number']:'' ?></td>
				<td><?= isset($u['user_ville'])?$u['user_ville']:'' ?></td>
				<td><?= isset($u['user_codepostal'])?dept(substr($u['user_codepostal'],0,2)):'' ?></td>
				<td><?= isset($u['user_codepostal'])?$u['user_codepostal']:'' ?></td>
				<td><?= isset($u['user_metiers'])?implode(', ',json_decode($u['user_metiers'])):'' ?></td>
				<td>
					<?= isset($u['hebergement'])?$u['hebergement']:'' ?>
					<img src="http://icons.iconarchive.com/icons/hopstarter/sleek-xp-basic/48/Ok-icon.png" class="check" />		
				</td>
				<td>
					<?= isset($u['covoit'])?$u['covoit']:'' ?>		
					<img src="http://icons.iconarchive.com/icons/hopstarter/sleek-xp-basic/48/Ok-icon.png"class="check" />		
				</td>
			</tr>

		<?php endforeach; ?>

	</tbody>
</table>