thinkphp-exception  

#基于thinkphp做的自定义异常类，可根据业务自定义错误码

exception('Index',1001);  
exception('Index',1001,'msg',['test'=>'test']);  
异常捕捉  
try {  
    throw new \Exception(1001);  
}catch (\Exception $e){  
    exception('Index',$e->getMessage());  
}  
返回成功  
Response::send('请求成功');  
Response::send('创建|修改|更新成功',ApiCode::CREATED);  
