import { chromium } from 'playwright';
const [,, url, js, width='1440'] = process.argv;
const b = await chromium.launch(); const c = await b.newContext({ viewport:{width:Number(width),height:900} });
await c.addCookies([{name:'playground_auto_login_already_happened',value:'1',domain:new URL(url).hostname,path:'/'}]);
const p = await c.newPage(); await p.goto(url,{waitUntil:'networkidle'});
const r = await p.evaluate(js); console.log(typeof r === 'string' ? r : JSON.stringify(r,null,1)); await b.close();
