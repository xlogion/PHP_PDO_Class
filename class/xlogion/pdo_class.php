<?php
namespace xlogion;
class pdo_class {
	private  $pdo_link;
	private  $res;
	private  $charset = 'utf8';
	public  $tablepre = '';
	private  $parameters;

	function __construct($db_config) {
		$allow_array=array('host','port','dbname','username','password','charset','tablepre');
		foreach ($db_config as $k=>$v) in_array($k,$allow_array) &&  $$k=$v;
		$this->charset=$charset;
		$this->tablepre=$tablepre;
		$dsn = "mysql:dbname=$dbname;host=$host;port=$port;";
		$this->tablepre=$tablepre;
		try {
			$sql_options=array(\PDO::ATTR_ERRMODE=>\PDO::ERRMODE_EXCEPTION,\PDO::ATTR_PERSISTENT=>false);
			if (version_compare(PHP_VERSION, '5.3.6') >= 0) {
				$sql_options[\PDO::MYSQL_ATTR_INIT_COMMAND]='set names '.$this->charset.'';
				$this->pdo_link = new \PDO($dsn, $username, $password,$sql_options);
			}
			else {
				$this->pdo_link = new \PDO($dsn, $username, $password,$sql_options);
				$this->pdo_link->exec("SET NAMES ".$this->charset."");
			}
		} 
		catch(\PDOException $ex){
			$this->get_error($ex->getMessage());
		}
		
	}

	function close() {
		$this->pdo_link = null;
	}

	function get_error($e) {
		echo iconv("UTF-8", "GB2312//IGNORE", $e);
		return true;
	}

	function free() {
		$this->res = null;
	}

	function bind($parameters)
	{	
		$this->parameters=$parameters;
		
	}



	function query($sql){
        $res = $this->pdo_link->prepare($sql);
        if($res){
			$this->res = $res;
			if (count($this->parameters)>0) {
				foreach($this->parameters as $k=>$v) {
					$this->res->bindParam(':'.$k,$v);
				}
			}	
			$this->res->execute();
			$this->parameters=array();
        }
		

    }

	function page($sql_array,$cursor=\PDO::FETCH_ASSOC){
		$where='';
		$orderby='';
		$groupby='';
		$temp=array();
		if ($sql_array['sql']=='') {
			$table = $this->tablepre.$sql_array['table'];
			if (is_array($sql_array['fields'])) {
				$fields=implode(',',$sql_array['fields']);
			}
			else {
				$fields=$sql_array['fields'];
			}
			if (is_array($sql_array['where'])) {
				foreach ($sql_array['where'] as $k=>$v) {
					$where!='' && $where.=' and ';
					if (strstr($k,'@')) {
						$where.=substr($k,1).$v;
					}
					else {
						$where.=$k."='".$v."'";
					}
				}
			}
			else {
				$where=$sql_array['where'];
			}
			$where!='' && $where='where '.$where;
			if (is_array($sql_array['orderby'])) {
				foreach ($sql_array['orderby'] as $k=>$v) {
					$orderby!='' && $orderby.=',';
					$orderby.=$k." ".$v."";
				}
			}
			else {
				$orderby=$sql_array['orderby'];
			}
			$orderby!='' && $orderby='order by '.$orderby;
			if (is_array($sql_array['groupby'])) {
				foreach ($sql_array['groupby'] as $k=>$v) {
					$groupby!='' && $groupby.=',';
					$groupby.=$k." ".$v."";
				}
			}
			else {
				$groupby=$sql_array['groupby'];
			}
			$groupby!='' && $groupby='group by '.$groupby;


			$sql="select $fields from $table $where $orderby $groupby";
		}
		else {
			$sql=$sql_array['sql'];
		}
		$this->query($sql);
		$columncount=$this->rowCount();
		if ($columncount<$sql_array['limit']) {
			$temp['max_page']=1;
			$temp['this_page']=$sql_array['page'];
			$temp['total']=$columncount;
			$temp['result']=$this->res->fetchAll($cursor);
			$temp['page']=array(1);
		}
		else {
			$temp['max_page']=ceil($columncount/$sql_array['limit']);
			if ($sql_array['page']>$temp['max_page']) $sql_array['page']=$temp['max_page'];
			$temp['this_page']=$sql_array['page'];
			$limit_s=($sql_array['page']-1)*$sql_array['limit'];
			$limit='limit '.$limit_s.','.$sql_array['limit'];	
			
			$temp['total']=$columncount;
			if ($sql_array['sql']=='') {
			$sql="select $fields from $table $where $orderby $groupby $limit";
			}
			else {
			$sql=$sql_array['sql']." $limit";
			}
			$this->query($sql);
			$temp['result']=$this->res->fetchAll($cursor);
			if ($temp['max_page']<=$sql_array['page_limit']) {
				for($i=1;$i<=$temp['max_page'];$i++){
					$temp['page'][]=$i;
				}
			}
			else {
				if ($sql_array['page']< ceil($sql_array['page_limit']/2))  $p_start=1;
				else $p_start=($sql_array['page']-ceil($sql_array['page_limit']/2)+1);
				$p_end=$p_start+($sql_array['page_limit']-1);
				if ($p_end>$temp['max_page']) {
					 $p_end=$temp['max_page'];
					 $p_start=$p_end-($sql_array['page_limit']-1);
				}
				for($i=$p_start;$i<=$p_end;$i++){
					$temp['page'][]=$i;
				}
			}
		}		
		$this->free();
		return $temp;
    }

	function select($sql_array,$cursor=\PDO::FETCH_ASSOC){
		$where='';
		$orderby='';
		$groupby='';
		$temp=array();
		if ($sql_array['sql']=='') {
			$table = $this->tablepre.$sql_array['table'];
			if (is_array($sql_array['fields'])) {
				$fields=implode(',',$sql_array['fields']);
			}
			else {
				$fields=$sql_array['fields'];
			}
			if (is_array($sql_array['where'])) {
				foreach ($sql_array['where'] as $k=>$v) {
					$where!='' && $where.=' and ';
					if (strstr($k,'@')) {
						$where.=substr($k,1).$v;
					}
					else {
						$where.=$k."='".$v."'";
					}
				}
			}
			else {
				$where=$sql_array['where'];
			}	
			$where!='' && $where='where '.$where;
			if (is_array($sql_array['orderby'])) {
				foreach ($sql_array['orderby'] as $k=>$v) {
					$orderby!='' && $orderby.=',';
					$orderby.=$k." ".$v."";
				}
			}
			else {
				$orderby=$sql_array['orderby'];
			}
			$orderby!='' && $orderby='order by '.$orderby;
			if (is_array($sql_array['groupby'])) {
				foreach ($sql_array['groupby'] as $k=>$v) {
					$groupby!='' && $groupby.=',';
					$groupby.=$k." ".$v."";
				}
			}
			else {
				$groupby=$sql_array['groupby'];
			}
			$groupby!='' && $groupby='group by '.$groupby;
			
			$limit=$sql_array['limit'];

			$limit!='' && $limit='limit '.$limit;
			
			$sql="select $fields from $table $where $orderby $groupby";
		}
		else {
			$sql=$sql_array['sql'];
		} 
		$this->query($sql);
		$columncount=$this->rowCount();
		if ($sql_array['first']==1) $temp=$this->res->fetch($cursor);
		else $temp=$this->res->fetchAll($cursor);
		
		$this->free();
		if ($sql_array['count']==1) {
			return array('count'=>$columncount,'result'=>$temp);

		}
		else {
			return $temp;
		}
    }

	function insert ($sql_array){
		$table = $this->tablepre.$sql_array['table'];
		$value='';
		if (is_array($sql_array['value'])) {
			foreach ($sql_array['value'] as $k=>$v) {
				$value!='' && $value.=',';
				$value.=$k."='".$v."'";
			}
		}
		else {
			$value=$sql_array['value'];
		}
		$sql="INSERT  $table SET $value";
		$this->query($sql);
		$temp['count']=$this->rowCount();
		$temp['insert_id']=$this->pdo_link->lastInsertId();
		return $temp;

	}

	function update ($sql_array){
		if ($sql_array['sql']=='') {
			$table = $this->tablepre.$sql_array['table'];
			$where='';
			if (is_array($sql_array['where'])) {
				foreach ($sql_array['where'] as $k=>$v) {
					$where!='' && $where.=' and ';
					if (strstr($k,'@')) {
						$where.=substr($k,1).$v;
					}
					else {
						$where.=$k."='".$v."'";
					}
				}
			}
			else {
				$where=$sql_array['where'];
			}

			if (is_array($sql_array['value'])) {
				foreach ($sql_array['value'] as $k=>$v) {
					$value!='' && $value.=',';
					$value.=$k."='".$v."'";
				}
			}
			else {
				$value=$sql_array['value'];
			}

			$where!='' && $where='where '.$where;
			$sql="update  $table SET $value $where";
		}
		else {
			$sql=$sql_array['sql'];
		} 
		$this->query($sql);
		$temp['count']=$this->rowCount();
		return $temp;

	}

	function del ($sql_array){
		$table = $this->tablepre.$sql_array['table'];
		$where='';

		if (is_array($sql_array['where'])) {
			foreach ($sql_array['where'] as $k=>$v) {
				$where!='' && $where.=' and ';
				if (strstr($k,'@')) {
					$where.=substr($k,1).$v;
				}
				else {
					$where.=$k."='".$v."'";
				}
			}
		}
		else {
			$where=$sql_array['where'];
		}
		
		if ($where!='') $where='where '.$where;
		$sql="delete from  $table $where";
		$this->query($sql);
		$temp['count']=$this->rowCount();
		return $temp;

	}
	
	function first($sql,$cursor=\PDO::FETCH_ASSOC) {
		$sql['limit']="0,1";
		$sql['first']="1";
		$re=$this->select($sql);
		return $re;
	}

	function fetch($sql,$cursor=\PDO::FETCH_ASSOC) {
		return $this->res->fetch($cursor);
	}

	function fetchAll($sql,$cursor=\PDO::FETCH_ASSOC) {
		return $this->res->fetchAll($cursor);
	}

	function rowCount() {
		return $this->res->rowCount();
	}
	
	function lastInsertId(){
		
        return $this->res->lastInsertId();

    }

}
?>