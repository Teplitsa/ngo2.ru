<?php
/**
 * Calendar funcitons
 **/

class TST_Calendar_Table {
	
	var $week_days = array('Пн','Вт','Ср','Чт','Пт','Сб','Вс');
	var $month;
	var $year;
	var $today = array();
	var $days_in_month;
	
	
	function __construct($month = null, $year = null){
		
		$this->setup_today();
		
		if($month && $year){
			$this->month = $month;
			$this->year =  $year;
		}
		else {
			//get data from today
			$this->month = $this->today['m'];
			$this->year  = $this->today['y'];
		}
		
		$this->days_in_month = date('t',mktime(0,0,0,$month,1,$year));
		
	}
	
	function setup_today() {
       
        $today_exact = strtotime(sprintf('now %s hours', get_option('gmt_offset')));
                
		$this->today['d'] = date('d', $today_exact);
		$this->today['m'] = date('m', $today_exact);
		$this->today['y'] = date('Y', $today_exact);        
    }
	
	
	function prev_month_link(){		
		$text = '&lt;';
		
        //code to obtain url		
		$url = '';
		
		if(!empty($url)) {
			$link = "<a href='".esc_url($url)."'>{$text}</a>";
		}
		else {
			$link = "<span>{$text}</span>";
		}
        
		return $link;
	}
	
	function next_month_link(){		
		$text = '&gt;';
		
        //code to obtain url		
		$url = '';
		
		
		if(!empty($url)) {
			$link = "<a href='".esc_url($url)."'>{$text}</a>";
		}
		else {
			$link = "<span>{$text}</span>";
		}
        
		return $link;
	}
	
	function generate(){
		
		//marks
		$prev_link = $this->prev_month_link();
		$next_link = $this->next_month_link();
		$title = date_i18n('F Y', strtotime($this->year.'-'.$this->month.'-1'));
		
		//  table 
		$calendar = '<table cellpadding="0" cellspacing="0" class="calendar">';
		
		//month nav
		$calendar .= "<thead><tr class='calendar-month-nav'>";
		$calendar .= "<th colspan='2' class='prev'>{$prev_link}</th>"; //prev link
		$calendar .= "<th colspan='3' class='current'>{$title}</th>";
		$calendar .= "<th colspan='2' class='next'>{$next_link}</th></tr></thead>"; //next link
				
		// table headings 		
		$calendar.= '<tbody><tr class="calendar-row"><td class="calendar-day-head">'.implode('</td><td class="calendar-day-head">',$this->week_days).'</td></tr>';
	
		// days and weeks vars now ... 
		$running_day = date('w',mktime(0,0,0,$this->month,1,$this->year));
		if($running_day == 0)
			$running_day = 7;
			
		$days_in_this_week = 1;
		$day_counter = 0;
		$dates_array = array();
	
		// row for week one 
		$calendar.= '<tr class="calendar-row">';
	
		/* print "blank" days until the first of the current week */
		for($x = 1; $x < $running_day; $x++):
			$calendar.= '<td class="calendar-day-np"> </td>';
			$days_in_this_week++;
		endfor;
	
		// keep going with days.... 
		for($list_day = 1; $list_day <= $this->days_in_month; $list_day++):
			$calendar.= '<td class="calendar-day"><div class="calendar-cell">';
			
				//day content 
				$calendar.= '<span class="day-number">'.$list_day.'</span>';
				
				//links of items
				$calendar .= $this->day_content($list_day);
				
			$calendar.= '</div></td>';
			if($running_day == 7):
				$calendar.= '</tr>';
				if(($day_counter+1) != $this->days_in_month):
					$calendar.= '<tr class="calendar-row">';
				endif;
				$running_day = 0;
				$days_in_this_week = 0;
			endif;
			$days_in_this_week++; $running_day++; $day_counter++;
		endfor;
	
		// finish the rest of the days in the week */
		if($days_in_this_week < 8):
			for($x = 1; $x <= (8 - $days_in_this_week); $x++):
				$calendar.= '<td class="calendar-day-np"> </td>';
			endfor;
		endif;
	
		// final row 
		$calendar.= '</tr>';
	
		// end the table 
		$calendar.= '</tbody></table>';
		
		
		
		return $calendar;
	}

	function day_content($day){
		//2015-07-03
		$args = array(
			'post_type' => 'event',
			'posts_per_page' => -1,
			'meta_query' => array(
				array(
					'key' => 'event_date',
					'value' => $this->year.$this->month.zeroise($day, 2),
					'compare' => '=',
				),
			)
		);
		
		$query = new WP_Query($args); 
		if(!$query->have_posts())
			return '';
		
		$links = array();
		$modals = array();
		
		//@to_do: markup for modals
		foreach($query->posts as $event){
			$title_full = get_the_title($event->ID);
			$icon = '<i class="material-icons">event</i>';
			$m_id = 'e-modal-'.$event->ID;
			
			$links[] = "<a href='".get_permalink($event)."' title='".esc_attr($title_full)."' class='day-link mdl-button mdl-js-button mdl-button--icon' data-emodal='#{$m_id}'>{$icon}</a>";
			
			$modals[] = $this->create_modal($event);
		}
		
		$out = implode('', $links); 
		$out .= implode('', $modals);
		
		return $out;
	}
	
	function create_modal($event){
		
		$m_id = 'e-modal-'.$event->ID;
		
		$out = "<div id='{$m_id}' class='event-modal-content tpl-event'>";
		$out .= tst_events_card_content($event);
		$out .= "</div>";
				
		return $out;
	}
	
} //class end