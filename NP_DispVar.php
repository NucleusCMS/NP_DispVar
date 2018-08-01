<?php

/*
2009-07-01 NP_DispVar  Ver.0.1.3 (yama) added category var
2008-11-11 NP_BlogInfo Ver.0.1.2 (ephemera) added the function:
2008-08-19 NP_BlogInfo Ver.0.1.1 (ephemera) united NP_Info with NP_BlogSetting: http://japan.nucleuscms.org/bb/viewtopic.php?p=23237
2006-03-01 NP_BlogSetting Ver.0.1.1 (cha_cya) added the function: http://japan.nucleuscms.org/bb/viewtopic.php?p=11252
2005-10-23 NP_BlogInfo Ver.0.1.0 (ava) released the first source: http://japan.nucleuscms.org/bb/viewtopic.php?p=9382
2004-07-22 NP_BlogSetting Ver.0.1.0 (Taka) released the first source: http://japan.nucleuscms.org/bb/viewtopic.php?p=2765
*/

class NP_DispVar extends NucleusPlugin
{
	function getName()           {return 'DispVar';}
	function getEventList()      {return array();}
	function getAuthor()         {return 'Taka + cha_cya + ava + ephemera + yama';}
	function getURL()            {return 'http://japan.nucleuscms.org/bb/viewtopic.php?p=23237';}
	function getVersion()        {return '0.1.3';}
	function supportsFeature($w) {return ($w == 'SqlTablePrefix') ? 1 : 0;}
	function getDescription()    {return 'DispVar';}
	
	function doSkinVar($skinType, $mode, $varName, $specify = '')
	{
		$str = $this->getVar($skinType, $mode, $varName, $specify, 'skin');
		echo $str;
	}
	
	function doTemplateVar(&$item, $mode, $varName, $specify = '')
	{
		$str = $this->getVar($item, $mode, $varName, $specify, 'template');
		echo $str;
	}
	
	function getVar($item, $mode, $varName, $specify, $source)
	{
		global $blogid, $manager;
		$specify = intval($specify);
		switch ($mode)
		{
			case 'global'   : break;
			case 'blog'     : 
							$m_blogid = $specify;
							if($m_blogid)                 { $bid = $m_blogid; }
							if    ($source == 'skin')     { $bid = ($bid) ? $bid : $blogid; }
							elseif($source == 'template') {
							                                $itemid = $item->itemid;
							                                $bid = ($bid) ? $bid : getBlogIDFromItemID($itemid);
							                              }
							$blog =& $manager->getBlog($bid);
							$str = $this->getBlogVar($varName, $bid, $blog, $m_blogid);
							break;
			case 'category' :
			case 'cat'      : $str = $this->getCategoryVar($varName, $specify); break;
			case 'member'   : $str = $this->getMemberVar();   break;
			case 'skin'     : break;
			case 'item'     : break;
			default: break;
		}
		return $str;
	}
	
	function getBlogVar($varName, $bid, $blog)
	{
		global $manager;
		switch ($varName)
		{
			case 'id'   : $str =  $bid;    break;
			case 'name' : $str = $blog->getName(); break;
			case 'desc' : $str = $blog->getDescription(); break;
			case 'short': $str = $blog->getShortName(); break;
			case 'url'  : $str = $blog->getURL(); break;
			default: break;
		}
		return $str;
	}
	
	function getCategoryVar($varName, $specify)
	{
		global $manager, $catid;
		$catid = ($specify) ? $specify : $catid;
		switch ($varName)
		{
			case 'id'   : $str = $catid;    break;
			case 'name' : $str = quickQuery('SELECT cname as result FROM ' . sql_table('category') . ' WHERE catid="' . $catid . '"'); break;
			case 'desc' : $str = quickQuery('SELECT cdesc as result FROM ' . sql_table('category') . ' WHERE catid="' . $catid . '"'); break;
			case 'url'  : $str = createCategoryLink($catid); break;
			default: break;
		}
		return $str;
	}
	function getMemberVar($varName, $bid, $b)
	{
		// member infomation ----------
		$m = new MEMBER;
		$m->readFromID($bid);
		if ($m)
		{
			switch ($varName)
			{
				case 'mname'     : $str = $m->getDisplayName(); break;
				case 'mrealname' : $str = $m->getRealName(); break;
				case 'mnotes'    : $str = $m->getNotes(); break;
				case 'murl'      : $str = $m->getURL(); break;
				case 'memail'    : $str = $m->getEmail(); break;
				case 'mid'       : $str = $m;break; // echo $m->getID();
			}
		}
		return $str;
	}
}
?>