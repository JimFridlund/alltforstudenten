<?php
/**
 * ForecastEngine (demo): generates simple month curves.
 * Later: use category schedules, pay dates, and timing rules.
 */
class ForecastEngine {

  public static function linearSeries($start, $end, $days){
    $out = [];
    for ($i=0; $i<$days; $i++){
      $t = ($days<=1) ? 0 : ($i/($days-1));
      $out[] = (int)round($start + ($end-$start)*$t);
    }
    return $out;
  }

  public static function bufferDays(array $series){
    for ($i=0; $i<count($series); $i++){
      if ($series[$i] < 0) return $i;
    }
    return count($series);
  }

  public static function svgPolyline(array $series, $w, $h, $pad){
    $min = min($series); $max = max($series);
    if ($min === $max){ $min -= 1; $max += 1; }
    $pts = [];
    $n = count($series);
    for ($i=0; $i<$n; $i++){
      $x = $pad + ($i * (($w-2*$pad)/max(1,($n-1))));
      $y = $pad + (($max - $series[$i]) * (($h-2*$pad)/($max-$min)));
      $pts[] = round($x,1).",".round($y,1);
    }
    return implode(" ", $pts);
  }
}
?>
