<?php
namespace Pages;
	class Page{
		private $total;//数据表中总记录数
		private $listRows;//每页显示行数
		private $limit;
		private $uri;
		private $pageNum;//总页数
		private $config =array('header'=>"条记录",'prev'=>"上一页",'next'=>"下一页",'first'=>"首 页",'last'=>"尾 页");//分页信息配置项
		private $listNum = 6;//页数列表个数
        
		
		
		
		private static $_self = null;
		public static function getInstance($total,$listRows,$parameter='') {
		    if (null == self::$_self) {
		        self::$_self = new Page ($total,$listRows,$parameter);
		    }
		
		    return self::$_self;
		}
		/*
		 * $total 总记录数
		 * $limitRows 每页显示的条数
		 * $parameter  url携带的参数
		 */

		private function __construct($total,$listRows,$parameter){
				$this->total =$total;
				$this->listRows =!empty($listRows)?$listRows:10;
				$this->uri =$this->getUri($parameter);
				$this->page =!empty($_GET["page"])? $_GET['page']:1;
				$this->pageNum=ceil($this->total/$this->listRows);
				$this->limit=$this->setLimit();
		}

		public function __get($args){
			if($args=='limit'){
					return $this->limit;
			}else{
					return null;
			}
		}

		//设置Limit属性
		private function setLimit(){
			return ($this->page-1)*$this->listRows.", {$this->listRows}";
		}

		//获取url
		private function getUri($parameter){
		   
		    $uri =$_SERVER["REQUEST_URI"].(strpos($_SERVER["REQUEST_URI"],"?")?'':'?').$parameter;
			$parse=parse_url($uri);
			if(isset($parse['query'])){
				parse_str($parse['query'],$params);
				unset($params['page']);
				$uri = $parse['path']."?".http_build_query($params);
			}
			return $uri;
		}

		//每页数据的开始位置
		private function start(){
			if($this->total==0){
				return 0;
			}else{
				return ($this->page-1)*$this->listRows+1;
			}
		}

		//每页数据的结束位置
		private function end(){
			return min($this->page*$this->listRows,$this->total);
		}

		//设置 ‘首页’
		private function first(){
			if($this->page==1){
				@$html.="";
			}else{
				@$html.="<li><a href='{$this->uri}&page=1'>{$this->config['first']}</a></li>";
			}
			return $html;
		}

		//设置 ‘上一页’
		private function prev(){
			if($this->page==1){
				@$html.="<li class='prev'><a href='javascript:;'>&lt;&lt;</a></li>";
			}else{
				@$html.="<li class='prev'><a href='{$this->uri}&page=".($this->page-1)."'>&lt;&lt;</a></li>";
			}
			return $html;
		}

		//设置 页数检索列表
		private function pageList(){
			$linkPage="";
			$inum=floor($this->listNum/2);

			for ($i=$inum; $i >=1; $i--) {
				$page=$this->page-$i;
				if($page<1){
					continue;
				}
				$linkPage.="<li ><a href='{$this->uri}&page={$page}'>{$page}</a></li>";
			}

			$linkPage.="<li class='active'><a>{$this->page}</a></li>";

			for ($i=1; $i<=$inum; $i++) {
				$page=$this->page+$i;
				if($page>$this->pageNum){
					continue;
				}
				$linkPage.="<li><a href='{$this->uri}&page={$page}'>{$page}</a></li>";
			}
			return $linkPage;
		}

		//设置 ‘下一页’
		private function next(){
			if($this->page==$this->pageNum){
				@$html.="<li class='next'><a href='javascript:;'>&gt;&gt;</a></li>";
			}else{
				@$html.="<li class='next'><a href='{$this->uri}&page=".($this->page+1)."'>&gt;&gt;</a></li>";
			}
			return $html;
		}

		//设置 ‘尾页’
		private function last(){
			if($this->page==$this->pageNum){
				@$html.="";
			}else{
				@$html.="<li class='next'><a href='{$this->uri}&page=".($this->pageNum)."'>{$this->config['last']}</a></li>";
			}

			return $html;
		}

		//设置 ‘跳转’
		private function goPage(){
			return '&nbsp;<input type="text" value="'.$this->page.'" onkeydown="javascript:if(event.keyCode==13)
			{var page=(this.value >'.$this->pageNum.')?'.$this->pageNum.':this.value;
			location=\''.$this->uri.'&page=\'+page+\'\'}" style="width:25px;margin-right:10px;"><input type="button" value="Go" onclick="javascript:
			var page=(this.previousSibling.value >'.$this->pageNum.')?'.$this->pageNum.':this.previousSibling.value;
			location=\''.$this->uri.'&page=\'+page+\'\'" >&nbsp;';
		}

		//显示分页信息
		function Fpage($display=array(0,1,2,3,4,5,6,7,8)){
		   // $html[1]="&nbsp;&nbsp;共<b>{$this->total}</b>{$this->config['header']}";
			//$html[1]="&nbsp;&nbsp;本页显示<b>".($this->end()-$this->start()+1)."</b>条，本页<b>{$this->start()}</b>-<b>{$this->end()}</b>";
			//$html[2]="&nbsp;&nbsp;第<b>{$this->page}</b>页&nbsp;&nbsp;";//  /{$this->pageNum}
			
		    $html[3]="<div class='paging_bootstrap'><ul class='pagination'>";
			$html[4]=$this->prev();
			$html[5]=$this->pageList();
			$html[6]=$this->next();
			//$html[7]=$this->last();
			$html[8]="</ul></div>";
			//$html[8]=$this->goPage();
			
			$fpage="";
			foreach ($display as $key) {
				$fpage.=$html[$key];
			}
			return $fpage;
		}
		
		
		//设置 页数检索列表
		private function BackPageList(){
		    $linkPage="";
		    $inum=floor($this->listNum/2);
		
		    for ($i=$inum; $i >=1; $i--) {
		        $page=$this->page-$i;
		        if($page<1){
		            continue;
		        }
		        $linkPage.="<li><a href='{$this->uri}&page={$page}'>{$page}</a></li>";
		    }
		
		    $linkPage.="<li class='on'><a>{$this->page}</a></li>";
		
		    for ($i=1; $i<=$inum; $i++) {
		        $page=$this->page+$i;
		        if($page>$this->pageNum){
		            continue;
		        }
		        $linkPage.="<li><a href='{$this->uri}&page={$page}'>{$page}</a></li>";
		    }
		    return $linkPage;
		}
		//设置 页数检索列表
		private function frontPageList(){
		    $linkPage="";
		    $inum=floor($this->listNum/2);
		
		    for ($i=$inum; $i >=1; $i--) {
		        $page=$this->page-$i;
		        if($page<1){
		            continue;
		        }
		        $linkPage.="<li><a href='{$this->uri}&page={$page}'>{$page}</a></li>";
		    }
		
		    $linkPage.="<li class='active'><a>{$this->page}</a></li>";
		
		    for ($i=1; $i<=$inum; $i++) {
		        $page=$this->page+$i;
		        if($page>$this->pageNum){
		            continue;
		        }
		        $linkPage.="<li><a href='{$this->uri}&page={$page}'>{$page}</a></li>";
		    }
		    return $linkPage;
		}
		
		
		//设置 ‘上一页’
		private function BackPrev(){
		    if($this->page==1){
		        @$html.="<li><a href='javascript:;'>&lt;&lt;</a></li>";
		    }else{
		        @$html.="<li><a href='{$this->uri}&page=".($this->page-1)."'>&lt;&lt;</a></li>";
		    }
		    return $html;
		}
		
		//设置 ‘下一页’
		private function BackNext(){
		    if($this->page==$this->pageNum){
		        @$html.="<li><a href='javascript:;'>&gt;&gt;</a></li>";
		    }else{
		        @$html.="<li><a href='{$this->uri}&page=".($this->page+1)."'>&gt;&gt;</a></li>";
		    }
		    return $html;
		}
		
		
		
		//显示分页信息
		function BackPage($display=array(0,1,2,3,4,5,6,7,8)){
		    // $html[1]="&nbsp;&nbsp;共<b>{$this->total}</b>{$this->config['header']}";
		    //$html[1]="&nbsp;&nbsp;本页显示<b>".($this->end()-$this->start()+1)."</b>条，本页<b>{$this->start()}</b>-<b>{$this->end()}</b>";
		    //$html[2]="&nbsp;&nbsp;第<b>{$this->page}</b>页&nbsp;&nbsp;";//  /{$this->pageNum}
		    	
		    $html[3]="<ul>";
		    $html[4]=$this->BackPrev();
		    $html[5]=$this->BackPageList();
		    $html[6]=$this->BackNext();
		    //$html[7]=$this->last();
		    $html[8]="</ul>";
		    //$html[8]=$this->goPage();
		    	
		    $fpage="";
		    foreach ($display as $key) {
		        $fpage.=$html[$key];
		    }
		    return $fpage;
		}
		
		//显示分页信息
		function FrontPage($display=array(0,1,2,3,4,5,6,7,8)){
		    // $html[1]="&nbsp;&nbsp;共<b>{$this->total}</b>{$this->config['header']}";
		    //$html[1]="&nbsp;&nbsp;本页显示<b>".($this->end()-$this->start()+1)."</b>条，本页<b>{$this->start()}</b>-<b>{$this->end()}</b>";
		    //$html[2]="&nbsp;&nbsp;第<b>{$this->page}</b>页&nbsp;&nbsp;";//  /{$this->pageNum}
		     
		    $html[3]="<ul>";
		    //$html[4]=$this->BackPrev();
		    $html[5]=$this->frontPageList();
		    //$html[6]=$this->BackNext();
		    //$html[7]=$this->last();
		    $html[8]="</ul>";
		    //$html[8]=$this->goPage();
		     
		    $fpage="";
		    foreach ($display as $key) {
		        $fpage.=$html[$key];
		    }
		    return $fpage;
		}
	
		
		
	}
?>