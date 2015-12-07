<?php
$title = "야옹위키";
require 'session.php';
include 'header.php';?>

    <div class="container">
      <div class="well well-sm"><b><?php echo $_GET['q'];?></b>에 대한 검색 결과, 4항목을 찾았습니다.</div>

      <div class="row">
        <div class="col-md-12">
          <h4><a href="#">김현준</a> <span class="badge">+92</span></h4> 
          <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
          <h5><a href="#">#야옹위키</a> <a href="#">#글로벌융합공학부</a></h5>
          <br/>
        </div>
        <div class="col-md-12">
          <h4><a href="#">글로벌융합공학부</a> <span class="badge">+4</span></h4>
          <p>Donec id elit non mi porta <mark>gravida</mark> at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
          <h5><a href="#">#공과대학</a> <a href="#">#학부</a></h5>
          <br/>
        </div>
        <div class="col-md-12">
          <h4><a href="#">NEST</a>  <span class="badge">+142</span></h4>
          <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
          <h5><a href="#">#국제캠퍼스 동아리</a> <a href="#">#용재하우스</a></h5>
          <br/>
       </div>
        <div class="col-md-12">
          <h4><a href="#">STAGE</a> <span class="badge">+15</span></h4>
          <p>Donec sed odio dui. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Vestibulum id ligula porta felis euismod semper. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.</p>
          <h5><a href="#">#국제캠퍼스 동아리</a> <a href="#">#YES ICT</a></h5>
          <br/>
        </div>
      </div>

      <div class="well well-sm">원하는 지식이 없다면, <a href="create.php"><b>직접 지식을 추가</b></a>해 보세요</div>

      <div class="text-center">
        <ul class="pagination">
          <li>
            <a href="#" aria-label="Previous">
              <span aria-hidden="true">&laquo;</span>
            </a>
          </li>
          <li><a href="#">1</a></li>
          <li><a href="#">2</a></li>
          <li><a href="#">3</a></li>
          <li><a href="#">4</a></li>
          <li><a href="#">5</a></li>
          <li>
            <a href="#" aria-label="Next">
              <span aria-hidden="true">&raquo;</span>
            </a>
          </li>
        </ul>
      </div>
<?php include 'footer.php';?>