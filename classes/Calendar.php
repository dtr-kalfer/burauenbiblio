<?php
// Modified from:
//
// PHP Calendar Class Version 1.4 (5th March 2001)
//
// Copyright David Wilkinson 2000 - 2001. All Rights reserved.
//
// This software may be used, modified and distributed freely
// providing this copyright notice remains intact at the head
// of the file.
//
// This software is freeware. The author accepts no liability for
// any loss or damages whatsoever incurred directly or indirectly
// from the use of this script. The author of this software makes
// no claims as to its fitness for any purpose whatsoever. If you
// wish to use this software you should first satisfy yourself that
// it meets your requirements.
//
// URL:   http://www.cascade.org.uk/software/php/calendar/
// Email: davidw@cascade.org.uk

// further modified to reduce size: FL 12 Jun 2013

class Calendar {
	/* Constructor for the Calendar class
	 */
	function __construct() { }

	/* Get the array of strings used to label the days of the week. This array contains seven
		 elements, one for each day of the week. The first entry in this array represents Sunday.
	 */
	function getDayNames() { return $this->dayNames; }

	/* Set the array of strings used to label the days of the week. This array must contain seven
		 elements, one for each day of the week. The first entry in this array represents Sunday.
	 */
	function setDayNames($names) { $this->dayNames = $names; }

	/* Get the array of strings used to label the months of the year. This array contains twelve
		 elements, one for each month of the year. The first entry in this array represents January.
	 */
	function getMonthNames() { return $this->monthNames; }

	/* Set the array of strings used to label the months of the year. This array must contain twelve
		 elements, one for each month of the year. The first entry in this array represents January.
	 */
	function setMonthNames($names) { $this->monthNames = $names; }

	/* Gets the start day of the week. This is the day that appears in the first column
		 of the calendar. Sunday = 0.
	 */
	function getStartDay() { return $this->startDay; }

	/* Sets the start day of the week. This is the day that appears in the first column
		 of the calendar. Sunday = 0.
	 */
	function setStartDay($day) { $this->startDay = $day; }

	/* Gets the start month of the year. This is the month that appears first in the year
		 view. January = 1.
	 */
	function getStartMonth() { return $this->startMonth; }

	/* Sets the start month of the year. This is the month that appears first in the year
		 view. January = 1.
	 */
	function setStartMonth($month) { $this->startMonth = $month; }

	/* Return the URL to link to in order to display a calendar for a given month/year.
		 You must override this method if you want to activate the "forward" and "back"
		 feature of the calendar.

		 Note: If you return an empty string from this function, no navigation link will
		 be displayed. This is the default behaviour.

		 If the calendar is being displayed in "year" view, $month will be set to zero.
	 */
	function getCalendarLink($month, $year) { return ""; }

	/* Return the URL to link to  for a given date.
		 You must override this method if you want to activate the date linking
		 feature of the calendar.

		 Note: If you return an empty string from this function, no navigation link will
		 be displayed. This is the default behaviour.
	 */
	function getDateLink($day, $month, $year) { return ""; }

	function getMonthClass($month, $year) {
		return "calendarMonth";
	}

	function getDateClass($day, $month, $year) {
		$today = getdate(time());
		if ($year == $today["year"]
		    && $month == $today["mon"]
		    && $day == $today["mday"]) {
			return "calendarToday";
		} else {
			return "calendar";
		}
	}
	function getDateId($day, $month, $year) {
		$date = sprintf("%d-%02d-%02d", $year, $month, $day);
		return "date-".$date;
	}
	function getDateHTML($day, $month, $year) {
		$link = $this->getDateLink($day, $month, $year);
		return (($link == "") ? $day : "<a href=\"$link\">$day</a>");
	}
	function getWeekDayHTML($wday, $month, $year) {
		return $this->dayNames[$wday];
	}
	function getMonthNameHTML($month, $year, $showYear) {
		$monthName = $this->monthNames[$month - 1];
		return $monthName . (($showYear > 0) ? " " . $year : "");
	}

	/* Return the HTML for the current month
	 */
	function getCurrentMonthView() {
		$d = getdate(time());
		return $this->getMonthView($d["mon"], $d["year"]);
	}


	/* Return the HTML for the current year
	 */
	function getCurrentYearView() {
		$d = getdate(time());
		return $this->getYearView($d["year"]);
	}

	function getCurrentVThreeMonthView() {
		$d = getdate(time());
		return $this->getVThreeMonthView($d["mon"], $d["year"]);
	}

	function getCurrentHThreeMonthView() {
		$d = getdate(time());
		return $this->getHThreeMonthView($d["mon"], $d["year"]);
	}

	/* Return the HTML for a specified month
	 */
	function getMonthView($month, $year) {
		return $this->getMonthHTML($month, $year, 1, 0);
	}

	/* Return the HTML for a specified year
	 */
	function getYearView($year) {
		return $this->getYearHTML($year);
	}

	function getVThreeMonthView($month, $year) {
		return $this->getVThreeMonthHTML($month, $year);
	}

	function getHThreeMonthView($month, $year) {
		return $this->getHThreeMonthHTML($month, $year);
	}

	/********************************************************************************
		The rest are private methods. No user-servicable parts inside.
		You shouldn't need to call any of these functions directly.
	*********************************************************************************/

	/* Calculate the number of days in a month, taking into account leap years.
	 */
	function getDaysInMonth($month, $year) {
		if ($month < 1 || $month > 12) {
			return 0;
		}
		$d = $this->daysInMonth[$month - 1];
		if ($month == 2) {
			// Check for leap year
			// Forget the 4000 rule, I doubt I'll be around then...
			if ($year%4 == 0) {
				if ($year%100 == 0) {
					if ($year%400 == 0) {
						$d = 29;
					}
				} else {
					$d = 29;
				}
			}
		}
		return $d;
	}

	/* Generate the HTML for a given month
	 */
	function getMonthHTML($m, $y, $showYear = 1, $showLinks = 0) {
		$s = "";
		$a = $this->adjustDate($m, $y);
		$month = $a[0];
		$year = $a[1];
		$daysInMonth = $this->getDaysInMonth($month, $year);
		$date = getdate(mktime(12, 0, 0, $month, 1, $year));
		$first = $date["wday"];
		$prev = $this->adjustDate($month - 1, $year);
		$next = $this->adjustDate($month + 1, $year);
		if ($showLinks == 1) {
		    $prevMonth = $this->getCalendarLink($prev[0], $prev[1]);
		    $nextMonth = $this->getCalendarLink($next[0], $next[1]);
		} else {
		    $prevMonth = "";
		    $nextMonth = "";
		}
		$monthClass = $this->getMonthClass($month, $year);
		$s .= "<table class=\"$monthClass\">\n";
		$s .= "<tr>\n";
		$s .= "<td align=\"center\" valign=\"top\">" . (($prevMonth == "") ? "&nbsp;" : '<a class="month-prev" href="'. $prevMonth.'>&lt;&lt;</a>')  . "</td>\n";
		$s .= "<td align=\"center\" valign=\"top\" class=\"calendarHeader\" colspan=\"5\">" . $this->getMonthNameHTML($month, $year, $showYear) . "</td>\n";
		$s .= "<td align=\"center\" valign=\"top\">" . (($nextMonth == "") ? "&nbsp;" : '<a class="month-next" href="'.$nextMonth.'">&gt;&gt;</a>')  . "</td>\n";
		$s .= "</tr>\n";
		$s .= "<tr>\n";
		$s .= "<td align=\"center\" valign=\"top\" class=\"calendarDayNames\">" . $this->getWeekDayHTML(($this->startDay)%7, $month, $year) . "</td>\n";
		$s .= "<td align=\"center\" valign=\"top\" class=\"calendarDayNames\">" . $this->getWeekDayHTML(($this->startDay+1)%7, $month, $year) . "</td>\n";
		$s .= "<td align=\"center\" valign=\"top\" class=\"calendarDayNames\">" . $this->getWeekDayHTML(($this->startDay+2)%7, $month, $year) . "</td>\n";
		$s .= "<td align=\"center\" valign=\"top\" class=\"calendarDayNames\">" . $this->getWeekDayHTML(($this->startDay+3)%7, $month, $year) . "</td>\n";
		$s .= "<td align=\"center\" valign=\"top\" class=\"calendarDayNames\">" . $this->getWeekDayHTML(($this->startDay+4)%7, $month, $year) . "</td>\n";
		$s .= "<td align=\"center\" valign=\"top\" class=\"calendarDayNames\">" . $this->getWeekDayHTML(($this->startDay+5)%7, $month, $year) . "</td>\n";
		$s .= "<td align=\"center\" valign=\"top\" class=\"calendarDayNames\">" . $this->getWeekDayHTML(($this->startDay+6)%7, $month, $year) . "</td>\n";
		$s .= "</tr>\n";

		// We need to work out what date to start at so that the first appears in the correct column
		$d = $this->startDay + 1 - $first;
		while ($d > 1) {
		    $d -= 7;
		}

		while ($d <= $daysInMonth) {
			$s .= "<tr>\n";
			for ($i = 0; $i < 7; $i++) {
				if ($d > 0 && $d <= $daysInMonth) {
					$s .= "<td align=\"right\" valign=\"top\"";
					$class = $this->getDateClass($d, $month, $year);
					if ($class) {
						$s .= ' class="'.H($class).'"';
					}
					$id = $this->getDateId($d, $month, $year);
					if ($id) {
						$s .= ' id="'.H($id).'"';
					}
					$s .= ">";
					$s .= $this->getDateHTML($d, $month, $year);
				} else {
					$s .= "<td align=\"right\" valign=\"top\">";
					$s .= "&nbsp;";
				}
				$s .= "</td>\n";
				$d++;
			}
			$s .= "</tr>\n";
		}
		$s .= "</table>\n";
		return $s;
	}

	/* Generate the HTML for a given year
	 */
	function getYearHTML($year) {
		$s = "";
		$pdate = $this->adjustDate($this->startMonth - 12, $year);
		$ndate = $this->adjustDate($this->startMonth + 12, $year);
		$prev = $this->getCalendarLink($pdate[0], $pdate[1]);
		$next = $this->getCalendarLink($ndate[0], $ndate[1]);
		$s .= "<table class=\"calendar\" border=\"0\">\n";
		$s .= "<tr>";
		$s .= "<td align=\"center\" valign=\"top\" align=\"left\">" . (($prev == "") ? "&nbsp;" : "<a class=\"year-prev\" href=\"$prev\">&lt;&lt;</a>")  . "</td>\n";
		$s .= "<td class=\"calendarHeader\" valign=\"top\" align=\"center\">" . (($this->startMonth > 1) ? $year . " - " . ($year + 1) : $year) ."</td>\n";
		$s .= "<td align=\"center\" valign=\"top\" align=\"right\">" . (($next == "") ? "&nbsp;" : "<a class=\"year-next\" href=\"$next\">&gt;&gt;</a>")  . "</td>\n";
		$s .= "</tr>\n";
		$s .= "<tr>";
		$s .= "<td class=\"calendar\" valign=\"top\">" . $this->getMonthHTML(0 + $this->startMonth, $year, 0) ."</td>\n";
		$s .= "<td class=\"calendar\" valign=\"top\">" . $this->getMonthHTML(1 + $this->startMonth, $year, 0) ."</td>\n";
		$s .= "<td class=\"calendar\" valign=\"top\">" . $this->getMonthHTML(2 + $this->startMonth, $year, 0) ."</td>\n";
		$s .= "</tr>\n";
		$s .= "<tr>\n";
		$s .= "<td class=\"calendar\" valign=\"top\">" . $this->getMonthHTML(3 + $this->startMonth, $year, 0) ."</td>\n";
		$s .= "<td class=\"calendar\" valign=\"top\">" . $this->getMonthHTML(4 + $this->startMonth, $year, 0) ."</td>\n";
		$s .= "<td class=\"calendar\" valign=\"top\">" . $this->getMonthHTML(5 + $this->startMonth, $year, 0) ."</td>\n";
		$s .= "</tr>\n";
		$s .= "<tr>\n";
		$s .= "<td class=\"calendar\" valign=\"top\">" . $this->getMonthHTML(6 + $this->startMonth, $year, 0) ."</td>\n";
		$s .= "<td class=\"calendar\" valign=\"top\">" . $this->getMonthHTML(7 + $this->startMonth, $year, 0) ."</td>\n";
		$s .= "<td class=\"calendar\" valign=\"top\">" . $this->getMonthHTML(8 + $this->startMonth, $year, 0) ."</td>\n";
		$s .= "</tr>\n";
		$s .= "<tr>\n";
		$s .= "<td class=\"calendar\" valign=\"top\">" . $this->getMonthHTML(9 + $this->startMonth, $year, 0) ."</td>\n";
		$s .= "<td class=\"calendar\" valign=\"top\">" . $this->getMonthHTML(10 + $this->startMonth, $year, 0) ."</td>\n";
		$s .= "<td class=\"calendar\" valign=\"top\">" . $this->getMonthHTML(11 + $this->startMonth, $year, 0) ."</td>\n";
		$s .= "</tr>\n";
		$s .= "</table>\n";
		return $s;
	}

	function getVThreeMonthHTML($month, $year) {
		$s = "";
		$pdate = $this->adjustDate($month - 2, $year);
		$ndate = $this->adjustDate($month + 2, $year);
		$prev = $this->getCalendarLink($pdate[0], $pdate[1]);
		$next = $this->getCalendarLink($ndate[0], $ndate[1]);
		$s .= "<table class=\"calendar\">\n";
		$s .= "<tr>\n";
		$s .= "<td valign=\"top\" align=\"left\">" . (($prev == "") ? "&nbsp;" : "<a href=\"$prev\">&lt;&lt;Previous</a>")  . "</td>\n";
		$s .= "<td valign=\"top\" align=\"right\">" . (($next == "") ? "&nbsp;" : "<a href=\"$next\">Next&gt;&gt;</a>")  . "</td>\n";
		$s .= "</tr>\n";
		$s .= "<tr>\n";
		$s .= "<td class=\"calendar\" valign=\"top\" colspan=\"2\">" . $this->getMonthHTML($month, $year) ."</td>\n";
		$s .= "</tr>\n";
		$s .= "<tr>\n";
		$s .= "<td class=\"calendar\" valign=\"top\" colspan=\"2\">" . $this->getMonthHTML($month+1, $year) ."</td>\n";
		$s .= "</tr>\n";
		$s .= "<tr>\n";
		$s .= "<td class=\"calendar\" valign=\"top\" colspan=\"2\">" . $this->getMonthHTML($month+2, $year) ."</td>\n";
		$s .= "</tr>\n";
		$s .= "</table>\n";
		return $s;
	}

	function getHThreeMonthHTML($month, $year) {
		$s = "";
		$pdate = $this->adjustDate($month - 2, $year);
		$ndate = $this->adjustDate($month + 2, $year);
		$prev = $this->getCalendarLink($pdate[0], $pdate[1]);
		$next = $this->getCalendarLink($ndate[0], $ndate[1]);
		$s .= "<table class=\"calendar\">\n";
		$s .= "<tr>\n";
		$s .= "<td valign=\"top\" align=\"left\">" . (($prev == "") ? "&nbsp;" : "<a href=\"$prev\">&lt;&lt;Previous</a>")  . "</td>\n";
		$s .= "<td></td>\n";
		$s .= "<td valign=\"top\" align=\"right\">" . (($next == "") ? "&nbsp;" : "<a href=\"$next\">Next&gt;&gt;</a>")  . "</td>\n";
		$s .= "</tr>\n";
		$s .= "<tr>\n";
		$s .= "<td class=\"calendar\" valign=\"top\">" . $this->getMonthHTML($month, $year) ."</td>\n";
		$s .= "<td class=\"calendar\" valign=\"top\">" . $this->getMonthHTML($month+1, $year) ."</td>\n";
		$s .= "<td class=\"calendar\" valign=\"top\">" . $this->getMonthHTML($month+2, $year) ."</td>\n";
		$s .= "</tr>\n";
		$s .= "</table>\n";
		return $s;
	}

	/* Adjust dates to allow months > 12 and < 0. Just adjust the years appropriately.
		 e.g. Month 14 of the year 2001 is actually month 2 of year 2002.
	 */
	function adjustDate($month, $year) {
		$a = array();
		$a[0] = $month;
		$a[1] = $year;
		while ($a[0] > 12) {
			$a[0] -= 12;
			$a[1]++;
		}
		while ($a[0] <= 0) {
			$a[0] += 12;
			$a[1]--;
		}
		return $a;
	}

	/* The start day of the week. This is the day that appears in the first column
		 of the calendar. Sunday = 0.
	 */
	var $startDay = 0;

	/* The start month of the year. This is the month that appears in the first slot
		 of the calendar in the year view. January = 1.
	 */
	var $startMonth = 1;

	/* The labels to display for the days of the week. The first entry in this array
		 represents Sunday.
	 */
	var $dayNames = array("S", "M", "T", "W", "T", "F", "S");

	/* The labels to display for the months of the year. The first entry in this array
		 represents January.
	 */
	var $monthNames = array("January", "February", "March", "April", "May", "June",
		"July", "August", "September", "October", "November", "December");

	/* The number of days in each month. You're unlikely to want to change this...
		 The first entry in this array represents January.
	 */
	var $daysInMonth = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
}

/* -------------------------------------------------------------------------- */
/* Imported Class ends here. Rest is OB specific.
/* -------------------------------------------------------------------------- */
	class EditingCalendar extends Calendar {
		function __construct($calendar, $start, $end) {
			$this->calendar = $calendar;
			$calendars = new Calendars;
			$rows = $calendars->getDays($calendar, $start, $end);
			$this->open = array();
			foreach ($rows as $row) {
				if ($row['open'] == 'No') {
					$this->open[$row['date']] = 'No';
				} else {
					$this->open[$row['date']] = 'Yes';
				}
			}
		}
		function getCalendarLink($month, $year) {
			$params = 'month='.U($month).'&year='.U($year);
			$params .= '&calendar='.U($this->calendar);
			return "../admin/calendarForm.php?".$params;
		}
		function getWeekDayHTML($wday, $month, $year) {
			$f = sprintf("toggleDays('%1d', '%04d', '%02d')",
				H($wday), H($year), H($month));
			return '<a onclick="'.$f.'">' . $this->dayNames[$wday] . '</a>';
		}
		function getMonthNameHTML($month, $year, $showYear) {
			$monthName = $this->monthNames[$month - 1];
			$f = sprintf("toggleDays('*', '%04d', '%02d')",
				H($year), H($month));
			return '<a onclick="'.$f.'">' . $monthName
				. (($showYear > 0) ? " " . $year : "") . '</a>';
		}
		function getDateHTML($day, $month, $year) {
		    $dayTags = array("Su", "Mo", "Tu", "We", "Th", "Fr", "Sa");
		    $date = sprintf("%04d-%02d-%02d", $year, $month, $day);
		    $class = ''; // initialize class

		    if (isset($this->open[$date]) && $this->open[$date] == 'Yes') {
		        $class .= "calendarOpen ";
		    } elseif (isset($this->open[$date]) && $this->open[$date] == 'No') {
		        $class .= "calendarHoliday ";
		    } else {
		        $class .= "calendarUnknown ";
		    }

		    $dt = getdate(mktime(0, 0, 0, $month, $day, $year));
		    $id = $dt['wday'].'-'.$date;
		    $tag = $dayTags[$dt['wday']];

		    return '<input type="hidden" id="IN-'.H($id).'" '
		        . 'name="IN-'.H($id).'" value="'.H($this->open[$date] ?? '').'" />'
		        . '<a class="'.$tag.' '.$class.'" onclick="toggleDay(\''.$id.'\')">'.$day.'</a>';
		}
		function getDateId($day, $month, $year) {
			$dt = getdate(mktime(0, 0, 0, $month, $day, $year));
			$date = sprintf("%1d-%04d-%02d-%02d", $dt['wday'], $year, $month, $day);
			return "date-".$date;
		}
		function getDateClass($day, $month, $year) {
			$class = "";
			$today = getdate(time());
			if ($year == $today["year"]
			    && $month == $today["mon"]
			    && $day == $today["mday"]) {
				$class .= "calendarToday ";
			}
			$date = sprintf("%d-%02d-%02d", $year, $month, $day);
			if ($this->open[$date] == 'Yes') {
				$class .= "calendarOpen ";
			} elseif ($this->open[$date] == 'No') {
				$class .= "calendarClosed ";
			} else {
				$class .= "calendarUnknown ";
			}
			return $class;
		}
	}
