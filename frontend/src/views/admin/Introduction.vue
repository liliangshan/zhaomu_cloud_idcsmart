<template>
  <div class="hlwidc-introduction-page">
    <div class="hlwidc-page-header">
      <h1>朝暮云前端集成指南</h1>
      <p class="hlwidc-page-subtitle">如何将朝暮云API调用绑定到前端网页</p>
    </div>

    <div class="hlwidc-content">
      <!-- 快速开始 -->
      <section class="hlwidc-section">
        <h2 class="hlwidc-section-title">
          <span class="hlwidc-icon">
            <svg class="icon" style="width: 1em;height: 1em;vertical-align: middle;fill: currentColor;overflow: hidden;" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg"><path d="M864 736c0-111.6-65.4-208-160-252.9V317.3c0-15.1-5.3-29.7-15.1-41.2L536.5 95.4C530.1 87.8 521 84 512 84s-18.1 3.8-24.5 11.4L335.1 276.1c-9.7 11.5-15.1 26.1-15.1 41.2v165.8C225.4 528 160 624.4 160 736h156.5c-2.3 7.2-3.5 15-3.5 23.8 0 22.1 7.6 43.7 21.4 60.8 11.3 14 26.2 24.6 43.1 30.6 23.1 54 75.6 88.8 134.5 88.8 29.1 0 57.3-8.6 81.4-24.8 23.6-15.8 41.9-37.9 53-64 16.9-6 31.8-16.5 43.1-30.5 13.8-17.2 21.4-38.8 21.4-60.8 0-8.4-1.1-16.4-3.1-23.8H864zM762.3 621.4c9.4 14.6 17 30.3 22.5 46.6H700V558.7c24.8 16.2 46.1 37.5 62.3 62.7zM388 483.1V318.8l124-147 124 147V668H388V483.1zM239.2 668c5.5-16.3 13.1-32 22.5-46.6 16.3-25.2 37.5-46.5 62.3-62.7V668h-84.8z m388.9 116.2c-5.2 3-11.2 4.2-17.1 3.4l-19.5-2.4-2.8 19.4c-5.4 37.9-38.4 66.5-76.7 66.5-38.3 0-71.3-28.6-76.7-66.5l-2.8-19.5-19.5 2.5c-1 0.1-2.1 0.2-3.3 0.2-4.9 0-9.6-1.3-13.8-3.7-8.7-5-14.1-14.3-14.1-24.4 0-10.6 5.9-19.4 14.6-23.8h231.3c8.8 4.5 14.6 13.3 14.6 23.8-0.1 10.2-5.5 19.6-14.2 24.5zM512 400m-48 0a48 48 0 1 0 96 0 48 48 0 1 0-96 0Z" /></svg>
          </span>
          快速开始
        </h2>
        <div class="hlwidc-section-content">
          <p>创建一个简单的HTML测试页面来预览朝暮云前端界面：</p>
          <div class="hlwidc-code-block">
            <pre><code>&lt;!DOCTYPE html&gt;
&lt;html lang="zh-CN"&gt;
&lt;head&gt;
    &lt;meta charset="UTF-8"&gt;
    &lt;meta name="viewport" content="width=device-width, initial-scale=1.0"&gt;
    &lt;title&gt;朝暮云 - 我的云服务器&lt;/title&gt;
    
    &lt;script&gt;
        // 全局变量配置
        window.APP_CONFIG = {
            isAdmin: false,
            version: '1.0.0',
            environment: 'development'
        }
    &lt;/script&gt;
    
    {php}echo (new \addons\zhaomu_cloud\ZhaoMuTemplete())->fetch();{/php}
&lt;/head&gt;
&lt;body&gt;
    &lt;div id="hlwidc-zhaomu-cloud"&gt;&lt;/div&gt;
&lt;/body&gt;
&lt;/html&gt;</code></pre>
          </div>
          <p>将此文件保存为 <code>mycloud.html</code> 并放置在 <code>addons/zhaomu_cloud/</code> 目录下，然后通过浏览器访问预览效果。</p>
        </div>
      </section>

      <!-- 集成步骤 -->
      <section class="hlwidc-section">
        <h2 class="hlwidc-section-title">
          <span class="hlwidc-icon">
            <svg class="icon" style="width: 1em;height: 1em;vertical-align: middle;fill: currentColor;overflow: hidden;" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg"><path d="M599.061868 216.612433 936.943248 216.612433c11.447726 0 20.729114-9.281387 20.729114-20.729114 0-11.447726-9.281387-20.729114-20.729114-20.729114L599.061868 175.154205c-11.447726 0-20.729114 9.281387-20.729114 20.729114C578.332754 207.330022 587.614142 216.612433 599.061868 216.612433z" fill="#272636" /><path d="M936.943248 332.694241 599.061868 332.694241c-11.447726 0-20.729114 9.281387-20.729114 20.729114 0 11.447726 9.281387 20.729114 20.729114 20.729114L936.943248 374.152469c11.447726 0 20.729114-9.281387 20.729114-20.729114C957.672362 341.975629 948.390975 332.694241 936.943248 332.694241z" fill="#272636" /><path d="M366.897228 65.290005 180.336228 65.290005c-62.966077 0-114.009613 51.043536-114.009613 114.009613l0 186.561c0 62.966077 51.043536 114.009613 114.009613 114.009613l186.561 0c62.966077 0 114.009613-51.043536 114.009613-114.009613L480.906841 179.299619C480.906841 116.334565 429.862281 65.290005 366.897228 65.290005zM439.448614 365.860619c0 40.068578-32.482809 72.551386-72.551386 72.551386L180.336228 438.412005c-40.068578 0-72.551386-32.482809-72.551386-72.551386L107.784842 179.299619c0-40.068578 32.482809-72.551386 72.551386-72.551386l186.561 0c40.068578 0 72.551386 32.482809 72.551386 72.551386L439.448614 365.860619z" fill="#272636" /><path d="M936.943248 653.992945 599.061868 653.992945c-11.447726 0-20.729114 9.281387-20.729114 20.729114s9.281387 20.729114 20.729114 20.729114L936.943248 695.451172c11.447726 0 20.729114-9.281387 20.729114-20.729114S948.390975 653.992945 936.943248 653.992945z" fill="#272636" /><path d="M936.943248 811.534004 599.061868 811.534004c-11.447726 0-20.729114 9.281387-20.729114 20.729114s9.281387 20.729114 20.729114 20.729114L936.943248 852.992231c11.447726 0 20.729114-9.281387 20.729114-20.729114S948.390975 811.534004 936.943248 811.534004z" fill="#272636" /><path d="M366.897228 544.129768 180.336228 544.129768c-62.966077 0-114.009613 51.043536-114.009613 114.009613l0 186.561c0 62.966077 51.043536 114.009613 114.009613 114.009613l186.561 0c62.966077 0 114.009613-51.043536 114.009613-114.009613L480.906841 658.139381C480.906841 595.173304 429.862281 544.129768 366.897228 544.129768zM439.448614 844.699358c0 40.068578-32.482809 72.551386-72.551386 72.551386L180.336228 917.250744c-40.068578 0-72.551386-32.482809-72.551386-72.551386L107.784842 658.139381c0-40.068578 32.482809-72.551386 72.551386-72.551386l186.561 0c40.068578 0 72.551386 32.482809 72.551386 72.551386L439.448614 844.699358z" fill="#272636" /></svg>
          </span>
          集成步骤
        </h2>
        <div class="hlwidc-section-content">
          <div class="hlwidc-steps">
            <div class="hlwidc-step">
              <div class="hlwidc-step-number">1</div>
              <div class="hlwidc-step-content">
                <h3>复制模板文件</h3>
                <p>将现有的简洁HTML模板复制一份，重命名为 <code>mycloud.html</code>：</p>
                <div class="hlwidc-code-block">
                  <pre><code>cp addons/wechat_common_notify/test.html addons/zhaomu_cloud/mycloud.html</code></pre>
                </div>
              </div>
            </div>

            <div class="hlwidc-step">
              <div class="hlwidc-step-number">2</div>
              <div class="hlwidc-step-content">
                <h3>修改HTML结构</h3>
                <p>删除原有内容，添加必要的配置和容器：</p>
                <div class="hlwidc-code-block">
                  <pre><code>&lt;script&gt;
    window.APP_CONFIG = {
        isAdmin: false,
        version: '1.0.0',
        environment: 'development'
    }
&lt;/script&gt;

{php}echo (new \addons\zhaomu_cloud\ZhaoMuTemplete())->fetch();{/php}

&lt;div id="hlwidc-zhaomu-cloud"&gt;&lt;/div&gt;</code></pre>
                </div>
              </div>
            </div>

            <div class="hlwidc-step">
              <div class="hlwidc-step-number">3</div>
              <div class="hlwidc-step-content">
                <h3>配置完成</h3>
                <p>HTML模板配置已完成，可以直接使用。</p>
              </div>
            </div>

            <div class="hlwidc-step">
              <div class="hlwidc-step-number">4</div>
              <div class="hlwidc-step-content">
                <h3>访问测试页面</h3>
                <p>通过浏览器访问 <code>http://your-domain.com/addons/zhaomu_cloud/mycloud.html</code> 预览效果。</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- 核心配置 -->
      <section class="hlwidc-section">
        <h2 class="hlwidc-section-title">
          <span class="hlwidc-icon">
            <svg class="icon" style="width: 1em;height: 1em;vertical-align: middle;fill: currentColor;overflow: hidden;" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg"><path d="M448.487619 97.52381l130.096762 0.170666c40.399238 0.073143 73.142857 32.792381 73.191619 73.216l0.048762 21.211429a345.283048 345.283048 0 0 1 71.143619 39.960381l17.408-10.044953a73.313524 73.313524 0 0 1 99.961905 26.819048l65.219047 112.566857a73.313524 73.313524 0 0 1-22.893714 97.816381l-3.974095 2.438095-17.481143 10.093715a341.479619 341.479619 0 0 1-1.292191 83.968l12.361143 7.168a73.313524 73.313524 0 0 1 28.867048 96.329142l-2.023619 3.803429-61.098667 105.813333a73.313524 73.313524 0 0 1-96.329143 28.867048l-3.803428-2.048-16.896-9.752381a341.918476 341.918476 0 0 1-68.291048 38.083048l0.024381 29.062095a73.313524 73.313524 0 0 1-68.754286 73.264762l-4.632381 0.146285-130.121142-0.170666a73.313524 73.313524 0 0 1-73.191619-73.216l-0.048762-35.035429a346.599619 346.599619 0 0 1-57.368381-34.035809l-31.158857 17.944381a73.313524 73.313524 0 0 1-99.986286-26.819048l-65.219048-112.566857a73.313524 73.313524 0 0 1 22.918095-97.816381l3.949715-2.438095 31.719619-18.285715c-2.438095-23.161905-2.56-46.665143-0.219429-70.119619l-35.206095-20.333714a73.313524 73.313524 0 0 1-28.891429-96.329143l2.048-3.803428 61.098667-105.813334a73.313524 73.313524 0 0 1 96.329143-28.867047l3.803429 2.048 30.72 17.724952a341.284571 341.284571 0 0 1 64.609523-39.716571l-0.048762-27.89181a73.313524 73.313524 0 0 1 68.754286-73.264762L448.487619 97.52381z m145.798095 283.721142a146.407619 146.407619 0 0 0-200.167619 53.638096 146.773333 146.773333 0 0 0 53.711238 200.362666 146.407619 146.407619 0 0 0 200.167619-53.638095 146.773333 146.773333 0 0 0-53.711238-200.362667z m-136.655238 90.258286a73.118476 73.118476 0 0 1 96.182857-28.842667l3.803429 2.048 3.657143 2.267429a73.508571 73.508571 0 0 1 23.210666 98.011429 73.118476 73.118476 0 0 1-99.961904 26.819047 73.48419 73.48419 0 0 1-26.892191-100.303238z" /></svg>
          </span>
          核心配置
        </h2>
        <div class="hlwidc-section-content">
          <div class="hlwidc-config-grid">
            <div class="hlwidc-config-item">
              <h4>PHP模板输出</h4>
              <p>在HTML中嵌入PHP代码输出资源文件：</p>
              <div class="hlwidc-code-block">
                <pre><code>{php}echo (new \addons\zhaomu_cloud\ZhaoMuTemplete())->fetch();{/php}</code></pre>
              </div>
            </div>

            <div class="hlwidc-config-item">
              <h4>Vue应用容器</h4>
              <p>指定Vue应用挂载的DOM容器：</p>
              <div class="hlwidc-code-block">
                <pre><code>&lt;div id="hlwidc-zhaomu-cloud"&gt;&lt;/div&gt;</code></pre>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- 功能特性 -->
      <section class="hlwidc-section">
        <h2 class="hlwidc-section-title">
          <span class="hlwidc-icon">
            <svg class="icon" style="width: 1em;height: 1em;vertical-align: middle;fill: currentColor;overflow: hidden;" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg"><path d="M511.872 0l168.64 321.728 343.168 69.312L784.768 659.2l43.392 364.544-316.288-156.032-316.352 156.032L238.784 659.2 0 391.104l343.168-69.312L511.872 0" /></svg>
          </span>
          功能特性
        </h2>
        <div class="hlwidc-section-content">
          <div class="hlwidc-feature-grid">
            <div class="hlwidc-feature-item">
              <h4>
                <svg class="icon" style="width: 1em;height: 1em;vertical-align: middle;fill: currentColor;overflow: hidden;margin-right: 8px;" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg"><path d="M617.92 688H448v-80h288a48 48 0 0 0 48-48v-128a48 48 0 0 0-48-48H448v-80h288a48 48 0 0 0 48-48V128a48 48 0 0 0-48-48H128a48 48 0 0 0-48 48v128a48 48 0 0 0 48 48h288v80H128a48 48 0 0 0-48 48v128a48 48 0 0 0 48 48h288v80H128a48 48 0 0 0-48 48v128a48 48 0 0 0 48 48h513.12a191.04 191.04 0 0 1-23.2-224zM672 160a32 32 0 1 1-32 32 32 32 0 0 1 32-32z m-496 64a16 16 0 0 1-32 0v-64a16 16 0 0 1 32 0z m64 0a16 16 0 0 1-32 0v-64a16 16 0 0 1 32 0z m64 0a16 16 0 0 1-32 0v-64a16 16 0 0 1 32 0z m80 0a32 32 0 0 1-32-32 32 32 0 0 1 32-32h160a32 32 0 0 1 32 32 32 32 0 0 1-32 32z m288 240a32 32 0 1 1-32 32 32 32 0 0 1 32-32z m-496 64a16 16 0 0 1-32 0v-64a16 16 0 0 1 32 0z m64 0a16 16 0 0 1-32 0v-64a16 16 0 0 1 32 0z m64 0a16 16 0 0 1-32 0v-64a16 16 0 0 1 32 0z m80 0a32 32 0 0 1-32-32 32 32 0 0 1 32-32h160a32 32 0 0 1 32 32 32 32 0 0 1-32 32zM176 832a16 16 0 0 1-32 0v-64a16 16 0 0 1 32 0z m64 0a16 16 0 0 1-32 0v-64a16 16 0 0 1 32 0z m64 0a16 16 0 0 1-32 0v-64a16 16 0 0 1 32 0z m262.56-9.44A32 32 0 0 1 544 832H384a32 32 0 0 1-32-32 32 32 0 0 1 32-32h160a32 32 0 0 1 32 32 32 32 0 0 1-9.44 22.56zM816 752a16 16 0 0 0-16-16h-48v32h48a16 16 0 0 0 16-16zM816 800h-64v32h64a16 16 0 0 0 0-32zM944 768a160 160 0 1 0-256 144 157.76 157.76 0 0 0 64 28.8A160 160 0 0 0 944 784v-16z m-128 96v16a16 16 0 0 1-32 0v-16h-16v16a16 16 0 0 1-32 0v-16h-16a16 16 0 0 1 0-32v-96a16 16 0 0 1 0-32h16v-16a16 16 0 0 1 32 0v16h16v-16a16 16 0 0 1 32 0v18.88A48 48 0 0 1 848 752a48.96 48.96 0 0 1-6.08 23.52A48 48 0 0 1 816 864z" /></svg>
                服务器管理
              </h4>
              <ul>
                <li>开机、关机、重启操作</li>
                <li>实时状态监控</li>
                <li>密码修改功能</li>
                <li>系统重装支持</li>
              </ul>
            </div>

            <div class="hlwidc-feature-item">
              <h4>
                <svg class="icon" style="width: 1em;height: 1em;vertical-align: middle;fill: currentColor;overflow: hidden;margin-right: 8px;" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg"><path d="M512 97.52381c228.912762 0 414.47619 185.563429 414.47619 414.47619s-185.563429 414.47619-414.47619 414.47619S97.52381 740.912762 97.52381 512 283.087238 97.52381 512 97.52381z m0 73.142857C323.486476 170.666667 170.666667 323.486476 170.666667 512s152.81981 341.333333 341.333333 341.333333 341.333333-152.81981 341.333333-341.333333S700.513524 170.666667 512 170.666667z m96.060952 120.441904l51.687619 51.687619L563.736381 438.857143H694.857143v73.142857h-146.285714v60.952381h146.285714v73.142857h-146.285714v97.52381h-73.142858v-97.52381h-146.285714v-73.142857h146.285714V512h-146.285714v-73.142857h131.145143l-96.060952-96.060953 51.736381-51.687619L512 387.120762l96.060952-96.060952z" /></svg>
                价格管理
              </h4>
              <ul>
                <li>多周期价格显示</li>
                <li>汇率自动转换</li>
                <li>折扣计算</li>
                <li>在线切换计费周期</li>
              </ul>
            </div>

            <div class="hlwidc-feature-item">
              <h4>
                <svg class="icon" style="width: 1em;height: 1em;vertical-align: middle;fill: currentColor;overflow: hidden;margin-right: 8px;" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg"><path d="M874.666667 277.333333a85.333333 85.333333 0 0 1 85.333333 85.333334v448a85.333333 85.333333 0 0 1-85.333333 85.333333h-85.333334a85.333333 85.333333 0 0 1-85.333333-85.333333V362.666667a85.333333 85.333333 0 0 1 85.333333-85.333334h85.333334zM554.666667 128a85.333333 85.333333 0 0 1 85.333333 85.333333v597.333334a85.333333 85.333333 0 0 1-85.333333 85.333333h-85.333334a85.333333 85.333333 0 0 1-85.333333-85.333333V213.333333a85.333333 85.333333 0 0 1 85.333333-85.333333h85.333334zM234.666667 490.666667a85.333333 85.333333 0 0 1 85.333333 85.333333v234.666667a85.333333 85.333333 0 0 1-85.333333 85.333333H149.333333a85.333333 85.333333 0 0 1-85.333333-85.333333V576a85.333333 85.333333 0 0 1 85.333333-85.333333h85.333334z m640-149.333334h-85.333334a21.333333 21.333333 0 0 0-21.184 18.837334L768 362.666667v448a21.333333 21.333333 0 0 0 18.837333 21.184L789.333333 832h85.333334a21.333333 21.333333 0 0 0 21.184-18.837333L896 810.666667V362.666667a21.333333 21.333333 0 0 0-18.837333-21.184L874.666667 341.333333zM554.666667 192h-85.333334a21.333333 21.333333 0 0 0-21.184 18.837333L448 213.333333v597.333334a21.333333 21.333333 0 0 0 18.837333 21.184L469.333333 832h85.333334a21.333333 21.333333 0 0 0 21.184-18.837333L576 810.666667V213.333333a21.333333 21.333333 0 0 0-18.837333-21.184L554.666667 192zM234.666667 554.666667H149.333333a21.333333 21.333333 0 0 0-21.184 18.837333L128 576v234.666667a21.333333 21.333333 0 0 0 18.837333 21.184L149.333333 832h85.333334a21.333333 21.333333 0 0 0 21.184-18.837333L256 810.666667V576a21.333333 21.333333 0 0 0-18.837333-21.184L234.666667 554.666667z" fill="#333333" /></svg>
                信息展示
              </h4>
              <ul>
                <li>服务器详细信息</li>
                <li>官方云数据同步</li>
                <li>配置信息展示</li>
                <li>时间信息管理</li>
              </ul>
            </div>

            <div class="hlwidc-feature-item">
              <h4>
                <svg class="icon" style="width: 1em;height: 1em;vertical-align: middle;fill: currentColor;overflow: hidden;margin-right: 8px;" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg"><path d="M153.6 469.312v469.376h716.8V469.312H153.6zM64 384h896v640H64V384z m403.2 329.92c-26.752-14.72-44.8-42.304-44.8-73.92 0-47.104 40.128-85.312 89.6-85.312 49.472 0 89.6 38.208 89.6 85.312 0 31.616-18.048 59.136-44.8 73.92v115.968a44.8 44.8 0 0 1-89.6 0v-115.968zM332.8 384h358.4V256c0-94.272-80.256-170.688-179.2-170.688-98.944 0-179.2 76.416-179.2 170.688v128zM512 0c148.48 0 268.8 114.56 268.8 256v128H243.2V256c0-141.44 120.32-256 268.8-256z" fill="#262626" /></svg>
                安全认证
              </h4>
              <ul>
                <li>实名认证检查</li>
                <li>Token验证</li>
                <li>权限控制</li>
                <li>错误处理机制</li>
              </ul>
            </div>
          </div>
        </div>
      </section>

      <!-- 测试验证 -->
      <section class="hlwidc-section">
        <h2 class="hlwidc-section-title">
          <span class="hlwidc-icon">
            <svg class="icon" style="width: 1em;height: 1em;vertical-align: middle;fill: currentColor;overflow: hidden;" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg"><path d="M42.666667 874.666667c-23.466667 0-42.666667-19.2-42.666667-42.666667V192c0-23.466667 19.2-42.666667 42.666667-42.666667h938.666666c23.466667 0 42.666667 19.2 42.666667 42.666667v640c0 23.466667-19.2 42.666667-42.666667 42.666667H42.666667z m42.666666-85.333334h853.333334V234.666667H85.333333v554.666666z" fill="#2F3CF4" /><path d="M733.866667 375.466667c-17.066667-17.066667-42.666667-17.066667-59.733334 0s-17.066667 42.666667 0 59.733333l76.8 76.8-76.8 76.8c-17.066667 17.066667-17.066667 42.666667 0 59.733333 8.533333 8.533333 19.2 12.8 29.866667 12.8s21.333333-4.266667 29.866667-12.8l106.666666-106.666666c17.066667-17.066667 17.066667-42.666667 0-59.733334l-106.666666-106.666666zM349.866667 375.466667c-17.066667-17.066667-42.666667-17.066667-59.733334 0l-106.666666 106.666666c-17.066667 17.066667-17.066667 42.666667 0 59.733334l106.666666 106.666666c8.533333 8.533333 19.2 12.8 29.866667 12.8s21.333333-4.266667 29.866667-12.8c17.066667-17.066667 17.066667-42.666667 0-59.733333L273.066667 512l76.8-76.8c17.066667-17.066667 17.066667-42.666667 0-59.733333M590.933333 300.8c-21.333333-8.533333-46.933333 2.133333-55.466666 25.6l-128 341.333333c-8.533333 21.333333 2.133333 46.933333 25.6 55.466667 4.266667 2.133333 10.666667 2.133333 14.933333 2.133333 17.066667 0 34.133333-10.666667 40.533333-27.733333l128-341.333333c8.533333-21.333333-4.266667-46.933333-25.6-55.466667" fill="#2F3CF4" /></svg>
          </span>
          测试验证
        </h2>
        <div class="hlwidc-section-content">
          <div class="hlwidc-test-steps">
            <div class="hlwidc-test-item">
              <h4>1. 检查资源加载</h4>
              <p>打开浏览器开发者工具，确认CSS和JS文件正确加载：</p>
              <div class="hlwidc-code-block">
                <pre><code>// 检查控制台是否有错误
// 确认网络面板中资源文件状态为200</code></pre>
              </div>
            </div>

            <div class="hlwidc-test-item">
              <h4>2. 功能测试</h4>
              <p>测试各项功能是否正常工作：</p>
              <ul>
                <li>服务器状态显示</li>
                <li>操作按钮响应</li>
                <li>弹窗显示正常</li>
                <li>价格计算准确</li>
              </ul>
            </div>
          </div>
        </div>
      </section>

      <!-- 常见问题 -->
      <section class="hlwidc-section">
        <h2 class="hlwidc-section-title">
          <span class="hlwidc-icon">
            <svg class="icon" style="width: 1em;height: 1em;vertical-align: middle;fill: currentColor;overflow: hidden;" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg"><path d="M512 1024A512 512 0 1 1 512 0a512 512 0 0 1 0 1024z m3.008-92.992a416 416 0 1 0 0-832 416 416 0 0 0 0 832zM457.792 684.8h130.56v128.128h-130.56V684.8zM320 397.44c0.576-29.952 5.696-57.408 15.36-82.368 9.728-24.96 23.296-46.592 40.832-64.896a183.68 183.68 0 0 1 63.168-42.88A213.76 213.76 0 0 1 522.24 192c39.36 0 72.256 5.376 98.56 16.192 26.368 10.88 47.616 24.32 63.68 40.384s27.584 33.408 34.56 52.032c6.912 18.56 10.368 35.84 10.368 51.968 0 26.624-3.456 48.512-10.432 65.728a162.048 162.048 0 0 1-59.904 75.328c-12.48 8.576-24.256 17.152-35.328 25.792a165.44 165.44 0 0 0-29.504 29.504c-8.64 11.072-14.08 24.96-16.256 41.6v31.616H465.6v-37.44c1.664-23.872 6.208-43.84 13.696-59.904 7.552-16.064 16.256-29.824 26.24-41.152 9.984-11.392 20.48-21.248 31.616-29.568 11.072-8.32 21.376-16.64 30.784-24.96 9.408-8.32 17.024-17.472 22.848-27.456 5.888-9.984 8.512-22.464 7.936-37.44 0-25.536-6.208-44.352-18.752-56.576-12.48-12.16-29.76-18.304-51.968-18.304-14.976 0-27.84 2.88-38.656 8.704a77.184 77.184 0 0 0-26.688 23.296 101.12 101.12 0 0 0-15.36 34.176 169.408 169.408 0 0 0-4.992 41.984H320z" fill="#262626" /></svg>
          </span>
          常见问题
        </h2>
        <div class="hlwidc-section-content">
          <div class="hlwidc-faq-list">
            <div class="hlwidc-faq-item">
              <h4>Q: 页面显示空白怎么办？</h4>
              <p>A: 检查浏览器控制台错误信息，确认资源文件路径正确，token参数有效。</p>
            </div>

            <div class="hlwidc-faq-item">
              <h4>Q: API调用失败怎么办？</h4>
              <p>A: 检查网络连接，确认API接口地址正确，验证token权限。</p>
            </div>

            <div class="hlwidc-faq-item">
              <h4>Q: 如何自定义样式？</h4>
              <p>A: 可以通过CSS覆盖默认样式，或者修改Vue组件中的样式定义。</p>
            </div>

            <div class="hlwidc-faq-item">
              <h4>Q: 支持移动端吗？</h4>
              <p>A: 是的，界面采用响应式设计，支持各种屏幕尺寸。</p>
            </div>
          </div>
        </div>
      </section>

      <!-- 技术支持 -->
      <section class="hlwidc-section">
        <h2 class="hlwidc-section-title">
          <span class="hlwidc-icon">
            <svg class="icon" style="width: 1em;height: 1em;vertical-align: middle;fill: currentColor;overflow: hidden;" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg"><path d="M512 64C264.6 64 64 264.6 64 512s200.6 448 448 448 448-200.6 448-448S759.4 64 512 64z m0 820c-205.4 0-372-166.6-372-372s166.6-372 372-372 372 166.6 372 372-166.6 372-372 372z" fill="#262626" /><path d="M464 336a48 48 0 1 0 96 0 48 48 0 1 0-96 0z m72 112h-48c-4.4 0-8 3.6-8 8v272c0 4.4 3.6 8 8 8h48c4.4 0 8-3.6 8-8V456c0-4.4-3.6-8-8-8z" fill="#262626" /></svg>
          </span>
          技术支持
        </h2>
        <div class="hlwidc-section-content">
          <div class="hlwidc-support-info">
            <p>如果您在集成过程中遇到技术问题，我们提供专业的前端集成技术支持服务：</p>
            
            <div class="hlwidc-contact">
              <p><strong>前端集成技术支持</strong></p>
              <div class="hlwidc-contact-info">
                <p><strong>微信：</strong>88172719</p>
                <p><strong>服务费用：</strong>20元/次</p>
                <p><strong>服务内容：</strong></p>
                <ul>
                  <li>前端集成问题诊断</li>
                  <li>代码调试与修复</li>
                  <li>样式定制指导</li>
                  <li>API接口对接支持</li>
                  <li>移动端适配优化</li>
                </ul>
                <p><strong>服务说明：</strong>提供一次性的技术问题解决，包括问题分析、代码修改建议和实现指导。</p>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup lang="ts">
// 这是一个静态介绍页面，不需要响应式数据
</script>

<style scoped>
.hlwidc-introduction-page {
  padding: 30px;
  max-width: 1200px;
  margin: 0 auto;
}

.hlwidc-page-header {
  text-align: center;
  margin-bottom: 40px;
  padding: 40px 0;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-radius: 16px;
}

.hlwidc-page-header h1 {
  margin: 0 0 16px 0;
  font-size: 2.5rem;
  font-weight: 700;
}

.hlwidc-page-subtitle {
  margin: 0;
  font-size: 1.2rem;
  opacity: 0.9;
}

.hlwidc-content {
  background: white;
  border-radius: 16px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.hlwidc-section {
  padding: 40px;
  border-bottom: 1px solid #e5e7eb;
}

.hlwidc-section:last-child {
  border-bottom: none;
}

.hlwidc-section-title {
  display: flex;
  align-items: center;
  gap: 12px;
  margin: 0 0 24px 0;
  font-size: 1.5rem;
  font-weight: 600;
  color: #1f2937;
}

.hlwidc-icon {
  font-size: 1.8rem;
}

.hlwidc-section-content {
  line-height: 1.6;
  color: #4b5563;
}

.hlwidc-steps {
  display: flex;
  flex-direction: column;
  gap: 32px;
}

.hlwidc-step {
  display: flex;
  gap: 20px;
  align-items: flex-start;
}

.hlwidc-step-number {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
  color: white;
  border-radius: 50%;
  font-weight: 600;
  font-size: 1.1rem;
  flex-shrink: 0;
}

.hlwidc-step-content h3 {
  margin: 0 0 12px 0;
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
}

.hlwidc-step-content p {
  margin: 0 0 16px 0;
  color: #4b5563;
}

.hlwidc-feature-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.hlwidc-feature-list li {
  position: relative;
  padding-left: 24px;
  margin-bottom: 8px;
  color: #4b5563;
}

.hlwidc-feature-list li::before {
  content: "✓";
  position: absolute;
  left: 0;
  color: #10b981;
  font-weight: bold;
}

.hlwidc-code-block {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 16px;
  margin: 16px 0;
  overflow-x: auto;
}

.hlwidc-code-block pre {
  margin: 0;
  font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
  font-size: 0.875rem;
  line-height: 1.5;
  color: #1f2937;
}

.hlwidc-api-list {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.hlwidc-api-item {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 20px;
}

.hlwidc-api-item h4 {
  margin: 0 0 12px 0;
  font-size: 1.1rem;
  font-weight: 600;
  color: #1f2937;
}

.hlwidc-api-details {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.hlwidc-method {
  display: inline-block;
  padding: 4px 8px;
  background: #3b82f6;
  color: white;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  width: fit-content;
}

.hlwidc-endpoint {
  font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
  font-size: 0.875rem;
  color: #059669;
  background: #f0fdf4;
  padding: 4px 8px;
  border-radius: 4px;
  width: fit-content;
}

.hlwidc-api-details p {
  margin: 0;
  color: #4b5563;
  font-size: 0.9rem;
}

.hlwidc-config-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 24px;
}

.hlwidc-config-item {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 20px;
}

.hlwidc-config-item h4 {
  margin: 0 0 12px 0;
  font-size: 1.1rem;
  font-weight: 600;
  color: #1f2937;
}

.hlwidc-config-item p {
  margin: 0 0 16px 0;
  color: #4b5563;
  font-size: 0.9rem;
}

.hlwidc-error-info {
  background: #fef2f2;
  border: 1px solid #fecaca;
  border-radius: 8px;
  padding: 20px;
}

.hlwidc-error-info h4 {
  margin: 0 0 12px 0;
  color: #dc2626;
  font-size: 1.1rem;
  font-weight: 600;
}

.hlwidc-error-info ul {
  margin: 12px 0;
  padding-left: 20px;
}

.hlwidc-error-info li {
  margin-bottom: 8px;
  color: #4b5563;
}

.hlwidc-practices {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.hlwidc-practice-item {
  background: #f0f9ff;
  border: 1px solid #bae6fd;
  border-radius: 8px;
  padding: 20px;
}

.hlwidc-practice-item h4 {
  margin: 0 0 12px 0;
  color: #0369a1;
  font-size: 1.1rem;
  font-weight: 600;
}

.hlwidc-practice-item p {
  margin: 0 0 16px 0;
  color: #4b5563;
  font-size: 0.9rem;
}

.hlwidc-practice-item ul {
  margin: 12px 0;
  padding-left: 20px;
}

.hlwidc-practice-item li {
  margin-bottom: 6px;
  color: #4b5563;
  font-size: 0.9rem;
}

.hlwidc-support-info {
  background: #f0fdf4;
  border: 1px solid #bbf7d0;
  border-radius: 8px;
  padding: 20px;
}

.hlwidc-support-info p {
  margin: 0 0 16px 0;
  color: #4b5563;
}

.hlwidc-contact {
  margin-top: 20px;
  padding: 16px;
  background: white;
  border-radius: 6px;
  border: 1px solid #d1d5db;
}

.hlwidc-contact p {
  margin: 0;
  color: #1f2937;
  font-weight: 500;
}

.hlwidc-contact-info {
  margin-top: 10px;
  padding-top: 10px;
  border-top: 1px solid #e5e7eb;
}

.hlwidc-contact-info p {
  margin: 5px 0;
  font-size: 0.9rem;
  color: #4b5563;
}

.hlwidc-feature-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 24px;
}

.hlwidc-feature-item {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 20px;
}

.hlwidc-feature-item h4 {
  margin: 0 0 12px 0;
  font-size: 1.1rem;
  font-weight: 600;
  color: #1f2937;
}

.hlwidc-feature-item ul {
  margin: 0;
  padding-left: 20px;
}

.hlwidc-feature-item li {
  margin-bottom: 6px;
  color: #4b5563;
  font-size: 0.9rem;
}

.hlwidc-test-steps {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.hlwidc-test-item {
  background: #f0f9ff;
  border: 1px solid #bae6fd;
  border-radius: 8px;
  padding: 20px;
}

.hlwidc-test-item h4 {
  margin: 0 0 12px 0;
  color: #0369a1;
  font-size: 1.1rem;
  font-weight: 600;
}

.hlwidc-test-item p {
  margin: 0 0 16px 0;
  color: #4b5563;
  font-size: 0.9rem;
}

.hlwidc-test-item ul {
  margin: 12px 0;
  padding-left: 20px;
}

.hlwidc-test-item li {
  margin-bottom: 6px;
  color: #4b5563;
  font-size: 0.9rem;
}

.hlwidc-faq-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.hlwidc-faq-item {
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 20px;
}

.hlwidc-faq-item h4 {
  margin: 0 0 8px 0;
  color: #1f2937;
  font-size: 1rem;
  font-weight: 600;
}

.hlwidc-faq-item p {
  margin: 0;
  color: #4b5563;
  font-size: 0.9rem;
  line-height: 1.5;
}

/* 响应式设计 */
@media (max-width: 768px) {
  .hlwidc-introduction-page {
    padding: 20px;
  }
  
  .hlwidc-page-header {
    padding: 30px 20px;
  }
  
  .hlwidc-page-header h1 {
    font-size: 2rem;
  }
  
  .hlwidc-section {
    padding: 24px 20px;
  }
  
  .hlwidc-step {
    flex-direction: column;
    gap: 16px;
  }
  
  .hlwidc-step-number {
    align-self: flex-start;
  }
  
  .hlwidc-config-grid {
    grid-template-columns: 1fr;
  }
}
</style>
