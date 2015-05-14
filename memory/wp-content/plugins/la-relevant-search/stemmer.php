<?php
/**
 * Stemmer Module (Default)
 * to-do add suport for English
 *
 * @package La relevant search
 */

$original_search_query = '';

class La_Search_Stemmer {
    
    function __construct($config) {
        
        $this->config = $config;
    }
    
    function plug(){
        
        add_filter('get_search_query', array($this, 'restore_search_query'), 1);
        add_action('parse_query', array($this, 'stem_search_query'));
    } 

    function  restore_search_query() {
        global $original_search_query;
        return $original_search_query;
    }

    function stem_search_query() {
        global $wp_query, $original_search_query;
        		
        if( !empty($wp_query->query_vars['s']) ) {
            if(empty($original_search_query))
                $original_search_query = $wp_query->query_vars['s'];
    
            $stemmer = $this->_get_stemmer_instance();
            if(empty($stemmer->VERSION))
                return;

            $words = preg_split('/[ ,\.\?!=]/u',$wp_query->query_vars['s']);
            foreach($words as &$word) {
                $word = $stemmer->stem_word($word);
            }
            $wp_query->query_vars['s'] = implode(' ', $words);
        }
    }

    protected function _get_stemmer_instance() {
        $classname = '';

        switch(get_locale()){
            case 'ru_RU':
                $classname = 'Lingua_Stem_Ru';
            // ...
            break;            
        }

        if(!empty($classname) && class_exists($classname))
            return new $classname();        
    }
} //class end

/**
 * Stemmer class
 * Code adopted from wp_stem_ru plugin by Yuri 'Bela' Belotitski
 * http://blog.portal.kharkov.ua/ */

class Lingua_Stem_Ru {
    var $VERSION = "0.02";
    var $Stem_Caching = 0;
    var $Stem_Cache = array();
    var $VOWEL = '/аеиоуыэюя/u';
    var $PERFECTIVEGROUND = '/((ив|ивши|ившись|ыв|ывши|ывшись)|((?<=[ая])(в|вши|вшись)))$/u';
    var $REFLEXIVE = '/(с[яь])$/u';
    var $ADJECTIVE = '/(ее|ие|ые|ое|ими|ыми|ей|ий|ый|ой|ем|им|ым|ом|его|ого|еых|ую|юю|ая|яя|ою|ею)$/u';
    var $PARTICIPLE = '/((ивш|ывш|ующ)|((?<=[ая])(ем|нн|вш|ющ|щ)))$/u';
    var $VERB = '/((ила|ыла|ена|ейте|уйте|ите|или|ыли|ей|уй|ил|ыл|им|ым|ены|ить|ыть|ишь|ую|ю)|((?<=[ая])(ла|на|ете|йте|ли|й|л|ем|н|ло|но|ет|ют|ны|ть|ешь|нно)))$/u';
    var $NOUN = '/(а|ев|ов|ие|ье|е|иями|ями|ами|еи|ии|и|ией|ей|ой|ий|й|и|ы|ь|ию|ью|ю|ия|ья|я)$/u';
    var $RVRE = '/^(.*?[аеиоуыэюя])(.*)$/u';
    var $DERIVATIONAL = '/[^аеиоуыэюя][аеиоуыэюя]+[^аеиоуыэюя]+[аеиоуыэюя].*(?<=о)сть?$/u';

	function Lingua_Stem_Ru() {
		mb_internal_encoding("UTF-8");
	}

    function s(&$s, $re, $to)
    {
        $orig = $s;
        $s = preg_replace($re, $to, $s);
        return $orig !== $s;
    }

    function m($s, $re)
    {
        return preg_match($re, $s);
    }

    function stem_word($word)
    {
        $word = mb_strtolower($word);

        $word = preg_replace("/ё/u","е",$word);
        # Check against cache of stemmed words
        if ($this->Stem_Caching && isset($this->Stem_Cache[$word])) {
            return $this->Stem_Cache[$word];
        }
        $stem = $word;
        do {
          if (!preg_match($this->RVRE, $word, $p)) break;
          $start = $p[1];
          $RV = $p[2];
          if (!$RV) break;

          # Step 1
          if (!$this->s($RV, $this->PERFECTIVEGROUND, '')) {
              $this->s($RV, $this->REFLEXIVE, '');

              if ($this->s($RV, $this->ADJECTIVE, '')) {
                  $this->s($RV, $this->PARTICIPLE, '');
              } else {
                  if (!$this->s($RV, $this->VERB, ''))
                      $this->s($RV, $this->NOUN, '');
              }
          }

          # Step 2
          $this->s($RV, '/и$/u', '');

          # Step 3
          if ($this->m($RV, $this->DERIVATIONAL))
              $this->s($RV, '/ость?$/u', '');

          # Step 4
          if (!$this->s($RV, '/ь$/u', '')) {
              $this->s($RV, '/ейше?/u', '');
              $this->s($RV, '/нн$/u', 'н');
          }

          $stem = $start.$RV;
        } while(false);
        if ($this->Stem_Caching) $this->Stem_Cache[$word] = $stem;
        return $stem;
    }

    function stem_caching($parm_ref)
    {
        $caching_level = @$parm_ref['-level'];
        if ($caching_level) {
            if (!$this->m($caching_level, '/^[012]$/')) {
                die(__CLASS__ . "::stem_caching() - Legal values are '0','1' or '2'. '$caching_level' is not a legal value");
            }
            $this->Stem_Caching = $caching_level;
        }
        return $this->Stem_Caching;
    }

    function clear_stem_cache()
    {
        $this->Stem_Cache = array();
    }
    
} //class end