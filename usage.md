## Table of contents

- [\MultiHttp\Http (abstract)](#class-multihttphttp-abstract)
- [\MultiHttp\Mime](#class-multihttpmime)
- [\MultiHttp\MultiRequest](#class-multihttpmultirequest)
- [\MultiHttp\Request](#class-multihttprequest)
- [\MultiHttp\Response](#class-multihttpresponse)
- [\MultiHttp\Exception\InvalidArgumentException](#class-multihttpexceptioninvalidargumentexception)
- [\MultiHttp\Exception\InvalidOperationException](#class-multihttpexceptioninvalidoperationexception)
- [\MultiHttp\Exception\UnexpectedResponseException](#class-multihttpexceptionunexpectedresponseexception)

<hr />

### Class: \MultiHttp\Http (abstract)

> Class Http

| Visibility | Function |
|:-----------|:---------|
| public | <strong>abstract delete(</strong><em>mixed</em> <strong>$uri</strong>, <em>array</em> <strong>$options=array()</strong>)</strong> : <em>mixed</em> |
| public | <strong>abstract get(</strong><em>mixed</em> <strong>$uri</strong>, <em>array</em> <strong>$options=array()</strong>)</strong> : <em>mixed</em> |
| public | <strong>abstract head(</strong><em>mixed</em> <strong>$uri</strong>, <em>array</em> <strong>$options=array()</strong>)</strong> : <em>mixed</em> |
| public | <strong>abstract options(</strong><em>mixed</em> <strong>$uri</strong>, <em>array</em> <strong>$options=array()</strong>)</strong> : <em>mixed</em> |
| public | <strong>abstract patch(</strong><em>mixed</em> <strong>$uri</strong>, <em>null</em> <strong>$payload=null</strong>, <em>array</em> <strong>$options=array()</strong>)</strong> : <em>mixed</em> |
| public | <strong>abstract post(</strong><em>mixed</em> <strong>$uri</strong>, <em>null</em> <strong>$payload=null</strong>, <em>array</em> <strong>$options=array()</strong>)</strong> : <em>mixed</em> |
| public | <strong>abstract put(</strong><em>mixed</em> <strong>$uri</strong>, <em>null</em> <strong>$payload=null</strong>, <em>array</em> <strong>$options=array()</strong>)</strong> : <em>mixed</em> |
| public | <strong>abstract trace(</strong><em>mixed</em> <strong>$uri</strong>, <em>array</em> <strong>$options=array()</strong>)</strong> : <em>mixed</em> |

<hr />

### Class: \MultiHttp\Mime

> Class to organize the Mime stuff a bit more

| Visibility | Function |
|:-----------|:---------|
| public static | <strong>getFullMime(</strong><em>string</em> <strong>$short_name</strong>)</strong> : <em>string full mime type (e.g. application/json)</em><br /><em>Get the full Mime Type name from a "short name". Returns the short if no mapping was found.</em> |
| public static | <strong>supportsMimeType(</strong><em>string</em> <strong>$short_name</strong>)</strong> : <em>bool</em> |

<hr />

### Class: \MultiHttp\MultiRequest

| Visibility | Function |
|:-----------|:---------|
| public | <strong>add(</strong><em>mixed</em> <strong>$method</strong>, <em>mixed</em> <strong>$uri</strong>, <em>mixed</em> <strong>$payload</strong>, <em>array</em> <strong>$options=array()</strong>)</strong> : <em>\MultiHttp\$this</em> |
| public | <strong>addOptions(</strong><em>array</em> <strong>$URLOptions</strong>)</strong> : <em>\MultiHttp\$this</em><br /><em>example: array(array('url'=>'http://localhost:9999/','timeout'=>1, 'method'=>'POST', 'data'=>'aa=bb&c=d'))</em> |
| public static | <strong>create()</strong> : <em>[\MultiHttp\MultiRequest](#class-multihttpmultirequest)</em> |
| public | <strong>import(</strong><em>[\MultiHttp\Request](#class-multihttprequest)</em> <strong>$request</strong>)</strong> : <em>\MultiHttp\$this</em> |
| public | <strong>sendAll()</strong> : <em>\MultiHttp\array(Response)</em> |
| public | <strong>setDefaults(</strong><em>array</em> <strong>$options=array()</strong>)</strong> : <em>\MultiHttp\$this</em> |
| protected | <strong>__construct()</strong> : <em>void</em><br /><em>MultiRequest constructor.</em> |
| protected static | <strong>prepare()</strong> : <em>void</em> |

<hr />

### Class: \MultiHttp\Request

> Class Request

| Visibility | Function |
|:-----------|:---------|
| public | <strong>addHeader(</strong><em>mixed</em> <strong>$headerName</strong>, <em>mixed</em> <strong>$value</strong>)</strong> : <em>\MultiHttp\$this</em> |
| public | <strong>addHeaders(</strong><em>array</em> <strong>$headers</strong>)</strong> : <em>\MultiHttp\$this</em> |
| public | <strong>addOptions(</strong><em>array</em> <strong>$options=array()</strong>)</strong> : <em>\MultiHttp\$this</em> |
| public | <strong>addQuery(</strong><em>mixed</em> <strong>$data</strong>)</strong> : <em>\MultiHttp\$this</em> |
| public | <strong>applyOptions()</strong> : <em>\MultiHttp\$this</em> |
| public static | <strong>create()</strong> : <em>[\MultiHttp\Request](#class-multihttprequest)</em> |
| public | <strong>delete(</strong><em>mixed</em> <strong>$uri</strong>, <em>array</em> <strong>$options=array()</strong>)</strong> : <em>[\MultiHttp\Request](#class-multihttprequest)</em> |
| public | <strong>endCallback()</strong> : <em>mixed</em> |
| public | <strong>expectsMime(</strong><em>string</em> <strong>$mime=`'json'`</strong>)</strong> : <em>\MultiHttp\$this</em> |
| public | <strong>get(</strong><em>mixed</em> <strong>$uri</strong>, <em>array</em> <strong>$options=array()</strong>)</strong> : <em>[\MultiHttp\Request](#class-multihttprequest)</em> |
| public | <strong>getIni(</strong><em>mixed</em> <strong>$field=null</strong>)</strong> : <em>bool/mixed</em> |
| public | <strong>hasEndCallback()</strong> : <em>bool</em> |
| public | <strong>head(</strong><em>mixed</em> <strong>$uri</strong>, <em>array</em> <strong>$options=array()</strong>)</strong> : <em>[\MultiHttp\Request](#class-multihttprequest)</em> |
| public | <strong>json(</strong><em>mixed</em> <strong>$body</strong>)</strong> : <em>string</em> |
| public | <strong>makeResponse(</strong><em>bool</em> <strong>$isMultiCurl=false</strong>)</strong> : <em>[\MultiHttp\Response](#class-multihttpresponse)</em> |
| public | <strong>onEnd(</strong><em>\callable</em> <strong>$callback</strong>)</strong> : <em>\MultiHttp\$this</em> |
| public | <strong>options(</strong><em>mixed</em> <strong>$uri</strong>, <em>array</em> <strong>$options=array()</strong>)</strong> : <em>[\MultiHttp\Request](#class-multihttprequest)</em> |
| public | <strong>patch(</strong><em>mixed</em> <strong>$uri</strong>, <em>null</em> <strong>$payload=null</strong>, <em>array</em> <strong>$options=array()</strong>)</strong> : <em>[\MultiHttp\Request](#class-multihttprequest)</em> |
| public | <strong>post(</strong><em>mixed</em> <strong>$uri</strong>, <em>null</em> <strong>$payload=null</strong>, <em>array</em> <strong>$options=array()</strong>)</strong> : <em>[\MultiHttp\Request](#class-multihttprequest)</em> |
| public | <strong>put(</strong><em>mixed</em> <strong>$uri</strong>, <em>null</em> <strong>$payload=null</strong>, <em>array</em> <strong>$options=array()</strong>)</strong> : <em>[\MultiHttp\Request](#class-multihttprequest)</em> |
| public | <strong>send(</strong><em>bool</em> <strong>$isMultiCurl=false</strong>)</strong> : <em>[\MultiHttp\Response](#class-multihttpresponse)</em> |
| public | <strong>sendMime(</strong><em>string</em> <strong>$mime=`'json'`</strong>)</strong> : <em>[\MultiHttp\Request](#class-multihttprequest)</em> |
| public | <strong>serializeBody()</strong> : <em>void</em> |
| public static | <strong>setLogHandler(</strong><em>\callable</em> <strong>$handler</strong>)</strong> : <em>void</em> |
| public | <strong>timeout(</strong><em>mixed</em> <strong>$timeout</strong>)</strong> : <em>\MultiHttp\$this</em> |
| public | <strong>trace(</strong><em>mixed</em> <strong>$uri</strong>, <em>array</em> <strong>$options=array()</strong>)</strong> : <em>[\MultiHttp\Request](#class-multihttprequest)</em> |
| public | <strong>unJson(</strong><em>mixed</em> <strong>$body</strong>)</strong> : <em>mixed</em> |
| public | <strong>uri(</strong><em>mixed</em> <strong>$uri</strong>)</strong> : <em>\MultiHttp\$this</em> |
| protected | <strong>__construct()</strong> : <em>void</em><br /><em>Request constructor.</em> |
| protected static | <strong>filterAndRaw(</strong><em>array</em> <strong>$options</strong>)</strong> : <em>array</em> |
| protected static | <strong>fullOption(</strong><em>mixed</em> <strong>$key</strong>)</strong> : <em>mixed</em> |
| protected | <strong>ini(</strong><em>mixed</em> <strong>$method</strong>, <em>mixed</em> <strong>$url</strong>, <em>mixed</em> <strong>$data</strong>, <em>array</em> <strong>$options=array()</strong>)</strong> : <em>\MultiHttp\$this</em> |
| protected | <strong>prepare()</strong> : <em>\MultiHttp\$this</em> |

*This class extends [\MultiHttp\Http](#class-multihttphttp-abstract)*

<hr />

### Class: \MultiHttp\Response

> Class Response

| Visibility | Function |
|:-----------|:---------|
| public static | <strong>create(</strong><em>[\MultiHttp\Request](#class-multihttprequest)</em> <strong>$request</strong>, <em>mixed</em> <strong>$body</strong>, <em>mixed</em> <strong>$info</strong>, <em>mixed</em> <strong>$errorCode</strong>, <em>mixed</em> <strong>$error</strong>)</strong> : <em>[\MultiHttp\Response](#class-multihttpresponse)</em> |
| public | <strong>hasErrors()</strong> : <em>bool Did we receive a 4xx or 5xx?</em><br /><em>Status Code Definitions Informational 1xx Successful    2xx Redirection   3xx Client Error  4xx Server Error  5xx http://pretty-rfc.herokuapp.com/RFC2616#status.codes</em> |
| public | <strong>parse()</strong> : <em>void</em> |
| public | <strong>unserializeBody()</strong> : <em>void</em> |
| protected | <strong>__construct()</strong> : <em>void</em><br /><em>Response constructor.</em> |

<hr />

### Class: \MultiHttp\Exception\InvalidArgumentException

> Class InvalidArgumentException

| Visibility | Function |
|:-----------|:---------|

*This class extends \LogicException*

*This class implements \Throwable*

<hr />

### Class: \MultiHttp\Exception\InvalidOperationException

> Class InvalidOperationException

| Visibility | Function |
|:-----------|:---------|

*This class extends \LogicException*

*This class implements \Throwable*

<hr />

### Class: \MultiHttp\Exception\UnexpectedResponseException

> Class UnexpectedResponseException

| Visibility | Function |
|:-----------|:---------|

*This class extends \UnexpectedValueException*

*This class implements \Throwable*

