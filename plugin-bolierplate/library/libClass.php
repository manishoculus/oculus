<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
 class bpLibrary{
     public $itemsPerPage;
     public $currentPage;

     function getPages($queryTotal, $type,$label,$search="",$param=""){

        $countNumber	= $queryTotal;	// get total results
        $pagestext 	    = '';
        $item	        = $label;
        $totalPages	    = ceil($countNumber / $this->itemsPerPage); // get total Pages
        $total			=  $countNumber;
        $currentStart	= ($this->itemsPerPage * ($this->currentPage - 1) + 1);
        $currentEnd	= ($this->itemsPerPage + $currentStart - 1);
        if($currentEnd > $total ){
             $currentEnd = $total;
         }
         $current		= $currentStart.' <i>to</i> '.$currentEnd;
         if( $totalPages > $this->currentPage){
             $nextPage		=	'&num='.($this->currentPage + 1);
             $prevPage		=	'&num='.($this->currentPage - 1);
         }else{
             $prevPage		=	'&num='.($this->currentPage - 1);
         }
         $lastPage		=	'&num='.$totalPages;
         $requestUrl	=	get_bloginfo("siteurl").'/wp-admin/admin.php?page='.$type;
         if($search!="")
         {
             $requestUrl .="&s=".$search;
         }
         if($param!="")
         {
             foreach($param as $k=>$v)
             {
                 $requestUrl .="&".$k."=".$v;
             }
         }
         if($this->currentPage > 1){
             $pageLinkFirst['link']	=	$requestUrl;
             $pageLinkFirst['class']	=	'';
         }else{
             $pageLinkFirst['link']	=	$requestUrl;
             $pageLinkFirst['class']	=	'disabled';
         }
         if($this->currentPage > 1){
             $pageLinkPrev['link']	=	$requestUrl.$prevPage;
             $pageLinkPrev['class']	=	'';
         }else{
             $pageLinkPrev['link']	=	$requestUrl;
             $pageLinkPrev['class']	=	'disabled';
         }

         if($this->currentPage <= $totalPages && $totalPages > 1){
             $pageLinkNext['link']	=	$requestUrl.$nextPage;
             if($this->currentPage == $totalPages){
                 $pageLinkNext['class']	=	'disabled';
             }else{
                 $pageLinkNext['class']	=	'';
             }
         }else{
             $pageLinkNext['link']	=	$requestUrl;
             $pageLinkNext['class']	=	'disabled';
         }

         if($this->currentPage <= $totalPages && $totalPages > 1){
             $pageLinkLast['link']	=	$requestUrl.$lastPage;
             if($this->currentPage == $totalPages){
                 $pageLinkLast['class']	=	'disabled';
             }else{
                 $pageLinkLast['class']	=	'';
             }
         }else{
             $pageLinkLast['link']	=	$requestUrl;
             $pageLinkLast['class']	=	'disabled';
         }

         $pagestext .= '<div class="tablenav-pages one-page">';
         if($countNumber > 0){
             if($countNumber > 1){
                 $item = $item.'s';
             }
             $pagestext .= '<span class="displaying-num">'.$countNumber.' '.$item.'</span>';
             if( $totalPages > 0){
                 $pagestext .= '<span class="pagination-links" style="display:block;">
							 <a href="'.$pageLinkFirst['link'].'" title="Go to the first page" class="first-page '.$pageLinkFirst['class'].'">&laquo;&laquo;</a>
							 <a href="'.$pageLinkPrev['link'].'" title="Go to the previous page" class="prev-page '.$pageLinkPrev['class'].'">&laquo;</a>
							 <span class="paging-input">'.$current.' of <span class="total-pages">'.$total.'</span></span>
							 <a href="'.$pageLinkNext['link'].'" title="Go to the next page" class="next-page '.$pageLinkNext['class'].'">&raquo;</a>
							 <a href="'.$pageLinkLast['link'].'" title="Go to the last page" class="last-page '.$pageLinkLast['class'].'">&raquo;&raquo;</a>
							</span>';
             }
         }else{
             $pagestext .= '<span class="displaying-num">0'. $item.'</span>';
         }
         $pagestext .= '</div>';
         return $pagestext;
     }
     function getPagination($numberOfPages, $className, $currentPage,$pageSize){
         $pages = '';
         $numberOfPages=ceil($numberOfPages/$pageSize);
         if($numberOfPages > 1){
             $pages .= '<ul class="paging">';

             if($currentPage!=0)
             {
                 $pages .="<li class='pagingtxt'>";
                 $pages .='<a href="#" class="'.$className.'" page="0">First</a>';
                 $pages .="</li>";
                 $pages .="<li class='pagingtxt'>";
                 $pages .='<a href="#" class="'.$className.'" page="'.($currentPage-1).'">Previous</a>';
                 $pages .="</li>";
             }

             for($i = 1; $i <= $numberOfPages; $i++){
                 $class = ($currentPage+1 == $i)? "currentPage" : '';

                 $pages .= '<li class='.$class.'>
                                <a href="#" class="'.$className.'" page="'.($i-1).'">'.$i.'</a>
                            </li>';
             }

             if($numberOfPages>($currentPage+1))
             {
                 $pages .="<li class='pagingtxt'>";
                 $pages .='<a href="#" class="'.$className.'" page="'.($currentPage+1).'">Next</a>';
                 $pages .="</li>";
                 $pages .="<li class='pagingtxt'>";
                 $pages .='<a href="#" class="'.$className.'" page="'.($numberOfPages-1).'">Last</a>';
                 $pages .="</li>";
             }
             $pages .= '</ul>';
         }
         return $pages;
     }
 }