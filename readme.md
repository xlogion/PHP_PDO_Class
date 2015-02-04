# `PHP PDO_class` 简介
----
- ## 说明
    
    ```
    PHP PDO_class
    
    是一个xlogion个人整理使用的PHP MYSQL PDO class，自带分页。
	尝试让它看上去更加的：结构清晰、使用简单、扩展性良好。

    ```
- ## 基础需求
    
    ```
    PHP>=5.3
	当然，你修改下命名空间，就可以支持低版本。
	
    是一个xlogion个人开发整理使用的PHP MYSQL PDO class。
	尝试让它看上去更加的：结构清晰、使用简单、扩展性良好。

    ```

----------	
# `PHP PDO_class` 基础使用
	
	```
	$db_config=array(
		'host' => XLOGION_DB_HOST,
		'port' => XLOGION_DB_PORT,
		'dbname' => XLOGION_DB_NAME,
		'username' => XLOGION_DB_USERNAME,
		'password' => XLOGION_DB_PASSWORD,
		'charset' => 'utf8',
		'tablepre' => 'test_'
		);

	$db = new xlogion\pdo_class($db_config);

	```



----------

# `db_config` 参数说明
- ## host

	数据库地址
	
- ## port

	数据库端口
		
- ## dbname

	数据库名
	
- ## username

	用户名
	
- ## password

	密码

- ## charset

	数据库字符编码

- ## tablepre

	表前缀

----------

# 系统约定（这里我们说好的）
- ## table

	表（不需要加上前缀`tablepre`）

- ## fields

	字段

- ## where

	条件

- ### @的使用

	在where里，比如id，前面加上@将不在生成 id=XX的格式，直接采用 idXX
	
	- #### 比如

	'id'=>1 //id=1
	'@id'=>'>0' //id>0
	

- ## orderby

	order by

- ## groupby

	group by

- ## value

	在插入，更新的时候生效，就是value

----------

# 通用表前缀
- ## tablepre

	$db->tablepre 
	请看以下示例，之后不在说明

----------

# 查询一条数据

- ## first

	效率会高于多条查询

- ### 例子
	$query=array(
				'table'=>'test',
				'fields'=>'*',
				'where'=>array(
							'id'=>1
							)
				);

	$temp=$db->first($query);

	----------

	$query=array(
				'table'=>'test',
				'fields'=>'*',
				'where'=>array(
							'@id'=>'=1'
							)
				);

	$temp=$db->first($query);

	----------

	$query=array(
				'table'=>'test',
				'fields'=>'*',
				'where'=>'id=1'
				);

	$temp=$db->first($query);

	----------

		$query=array('sql'=>'select * from test_test where id=1');

		or

		$query=array('sql'=>'select * from '.$db->tablepre.'test where id=1');

	$temp=$db->first($query);

	----------

	$db->bind(array('id'=>1));

		$query=array('sql'=>'select * from test_test where id=:id');

		or

		$query=array('sql'=>'select * from '.$db->tablepre.'test where id=:id');

	$temp=$db->first($query);

- ### 注意

	$db->bind可以循环多次使用，传递多个不同参数

- ### 返回

	$temp= Array ( [id] => 1 [title] => 1 [body] => 2 ) 

- ### 不在重复的声明

	以上示例的写法，适用于下面任何一个方法

----------

# 查询多条数据

- ## select

- ### 例子 
	同上，将	first 替换为 select
	只举一个例子
	
	$query=array(
					'table'=>'test',
					'fields'=>'*',
					'where'=>array(
								'@id'=>'>0'
								)
					);

	$temp=$db->select($query);

- ### 返回

	$temp = 

			Array(
				    [0] => Array
				        (
				            [id] => 1
				            [title] => 1
				            [body] => 2
				        )
				
				    [1] => Array
				        (
				            [id] => 2
				            [title] => 1
				            [body] => 3
				        )
				
				    [2] => Array
				        (
				            [id] => 3
				            [title] => 1
				            [body] => 1
				        )
				
				    [3] => Array
				        (
				            [id] => 4
				            [title] => 1
				            [body] => 1
				        )
				
				)


----------


# 分页方法

- ## page

- ### 增加的参数

	- #### page `页数`

	- #### limit	 `返回多少条记录`

	- #### page_limit `返回的页码（以当前页为中间值）`
	
		使用场景如下图红框

		![](https://raw.githubusercontent.com/xlogion/PHP_PDO_Class/master/img/page.png)

- ### 例子 
	

	$query=array(
			'table'=>'test',
			'fields'=>'*',
			'orderby'=>array(
						'@id'=>'>0'
						),
			'page'=>1,
			'limit'=>2,
			'page_limit'=>5
			);
	$temp=$db->page($query);

- ### 返回

	$temp = 

			Array
				(
				    [max_page] => 2
				    [this_page] => 1
				    [total] => 4
				    [result] => Array
				        (
				            [0] => Array
				                (
				                    [id] => 1
				                    [title] => 1
				                    [body] => 2
				                )
				
				            [1] => Array
				                (
				                    [id] => 2
				                    [title] => 1
				                    [body] => 3
				                )
				
				        )
				
				    [page] => Array
				        (
				            [0] => 1
				            [1] => 2
				        )
				
				)

	
	- #### max_page `最大页`

	- #### this_page	 `当前页`

	- #### total `总记录数`
	
	- #### result `结果集`

	- #### page `请看上图`

- ### 防溢出机制
	
		当你传递的page大于max_page时候，返回会自动过滤成最后一页

----------

# 插入一条数据

- ## insert
	注意数组里的`value`
- ### 例子 
	$query=array(
			'table'=>'test',
			'value'=>array(
					'title'=>'ttt',
					'body'=>'ttt'
					)
				);

	$temp=$db->insert($query);

- ### 返回
	$temp = 

			Array(
				    [count] => 1
				    [insert_id] => 5
				)
	

	- #### count 本次执行的记录数（可以用来判断插入是否成功）

	- #### insert_id 插入之后获取的ID

----------

# 更新一条数据

- ## update
	注意数组里的新加的`where`
- ### 例子 
	$query=array(
			'table'=>'test',
			'value'=>array(
					'title'=>'ttt',
					'body'=>'ttt'
					),
			'where'=>array(
					'id'=>'2'
					)
				);

	$temp=$db->update($query);

- ### 返回
	$temp = 

			Array(
				    [count] => 1
				)
	

	- #### count 本次执行的记录数（可以用来判断更新是否成功）


----------

# 删除一条数据

- ## del
	去除`value`即可 其实你保留的话，也不会执行
- ### 例子 
	$query=array(
			'table'=>'test',
			'where'=>array(
					'id'=>'2'
					)
				);

	$temp=$db->del($query);

- ### 返回
	$temp = 

			Array(
				    [count] => 1
				)
	

	- #### count 本次执行的记录数（可以用来判断删除是否成功）
