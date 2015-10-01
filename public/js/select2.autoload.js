/* 
 * Copyright (C) 2015 schurix
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


jQuery(function(){
	jQuery("select[data-fancy]").each(function(){
		$this = jQuery(this);
		if(!$this.select2){
			return;
		}
		var defaults = {
			allowClear : true,
			dropdownAutoWidth : true,
			width: "element"
		};
		if($this.attr('data-placeholder')){
			defaults.placeholder = $this.attr('data-placeholder');
		}
		if($this.attr('data-allow-clear')){
			defaults.allowClear = $this.attr('data-allow-clear');
		}
		if($this.attr('data-width-style')){
			defaults.width = $this.attr('data-width-style');
		}
		$this.select2(defaults);
	});
});