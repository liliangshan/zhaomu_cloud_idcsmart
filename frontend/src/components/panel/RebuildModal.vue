<template>
  <div v-if="show" class="hlwidc-modal-overlay" @click="closeModal">
    <div class="hlwidc-modal-content hlwidc-rebuild-modal" @click.stop>
      <div class="hlwidc-modal-header">
        <h3 class="hlwidc-modal-title">重装系统</h3>
        <button @click="closeModal" class="hlwidc-modal-close">×</button>
      </div>
      <div class="hlwidc-modal-body">
        <div class="hlwidc-rebuild-form">
          <div class="hlwidc-form-group">
            <label for="osTypeSelect">选择操作系统类型</label>
            <div v-if="imageLoading" class="hlwidc-loading-images">
              <div class="hlwidc-loading-spinner"></div>
              <span>正在加载镜像列表...</span>
            </div>
            <div v-else-if="imageError" class="hlwidc-error-images">
              <p>{{ imageError }}</p>
              <button @click="$emit('loadImages')" class="hlwidc-retry-btn">重试</button>
            </div>
            <div v-else class="hlwidc-os-type-selection">
              <div class="hlwidc-os-type-grid">
                <label 
                  v-for="group in imageGroups" 
                  :key="group.type" 
                  class="hlwidc-os-type-option"
                  :class="{ 'selected': selectedOsType === group.type }"
                >
                  <input 
                    type="radio" 
                    :value="group.type" 
                    v-model="selectedOsType"
                    name="osTypeSelect"
                    class="hlwidc-os-type-radio"
                  />
                  <div class="hlwidc-os-type-content">
                    <div class="hlwidc-os-type-icon">
                      <!-- Ubuntu 图标 -->
                      <svg v-if="group.type === 'Ubuntu'" viewBox="0 0 1024 1024" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M512 15.491679C237.797277 15.491679 16.266263 238.571861 16.266263 511.225416s221.531014 495.733737 495.733737 495.733737 495.733737-221.531014 495.733737-495.733737S786.202723 15.491679 512 15.491679z m105.343419 185.900152c17.040847-30.983359 57.319213-40.278366 86.753404-23.237519 30.983359 17.040847 40.278366 57.319213 23.237519 86.753404-17.040847 30.983359-57.319213 40.278366-86.753404 23.237518-30.983359-17.040847-40.278366-55.770045-23.237519-86.753403zM191.322239 574.741301c-35.630862 0-63.515885-27.885023-63.515885-63.515885s27.885023-63.515885 63.515885-63.515885 63.515885 27.885023 63.515885 63.515885-29.434191 63.515885-63.515885 63.515885z m55.770045 6.196672c44.92587-35.630862 44.92587-103.794251 0-139.425114 17.040847-65.065053 58.868381-120.835098 113.089259-158.015128l48.024206 79.007564c-102.245083 72.810893-102.245083 224.629349 0 297.440242l-48.024206 79.007564c-54.220877-35.630862-96.048411-91.400908-113.089259-158.015128z m457.004539 263.358547c-30.983359 17.040847-69.712557 7.74584-86.753404-23.237518-17.040847-30.983359-7.74584-69.712557 23.237519-86.753404 30.983359-17.040847 69.712557-7.74584 86.753404 23.237518 18.590015 30.983359 7.74584 69.712557-23.237519 86.753404z m1.549168-137.875945c-52.67171-20.139183-111.540091 13.942511-120.835098 69.712557-10.844175 3.098336-97.597579 27.885023-193.645991-18.590016l44.92587-80.556732c114.638427 52.67171 246.3177-23.237519 257.161876-148.720121l91.400907 1.549168c-3.098336 68.163389-34.081694 130.130106-79.007564 176.605144zM693.252648 495.733737c-10.844175-123.933434-142.523449-202.940998-257.161876-148.720121l-44.92587-80.556732c96.048411-48.024206 182.801815-21.688351 193.645991-18.590016 9.295008 57.319213 68.163389 89.85174 120.835098 69.712557 46.475038 46.475038 75.909228 108.441755 80.556732 176.605144l-92.950075 1.549168z"/>
                      </svg>
                      <!-- CentOS 图标 -->
                      <svg v-else-if="group.type === 'CentOS'" viewBox="0 0 1024 1024" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M512 0 392.533333 119.466667 469.333333 119.466667l8.533333 0L477.866667 128l0 290.133333 34.133333 34.133333 38.4-38.4L550.4 128 550.4 119.466667l8.533333 0 72.533333 0C631.466667 119.466667 512 0 512 0zM567.466667 136.533333l0 264.533333 170.666667-170.666667-89.6-89.6C648.533333 136.533333 567.466667 136.533333 567.466667 136.533333zM674.133333 136.533333l85.333333 85.333333 4.266667 4.266667-4.266667 4.266667L567.466667 426.666667l0 34.133333 34.133333 0 192-192 4.266667-4.266667 4.266667 4.266667 85.333333 85.333333 0-213.333333C887.466667 136.533333 674.133333 136.533333 674.133333 136.533333zM136.533333 136.533333l0 209.066667 81.066667-81.066667 4.266667-4.266667 4.266667 4.266667 192 192 34.133333 0L452.266667 426.666667 264.533333 234.666667 260.266667 226.133333l4.266667-4.266667 81.066667-81.066667L136.533333 136.533333 136.533333 136.533333zM375.466667 136.533333 285.866667 226.133333l174.933333 174.933333L460.8 136.533333C460.8 136.533333 375.466667 136.533333 375.466667 136.533333zM797.866667 285.866667l-170.666667 170.666667 260.266667 0L887.466667 375.466667C887.466667 375.466667 797.866667 285.866667 797.866667 285.866667zM226.133333 285.866667 136.533333 375.466667l0 85.333333 260.266667 0L226.133333 285.866667zM119.466667 392.533333 0 512l119.466667 119.466667L119.466667 554.666667l0-8.533333L128 546.133333l290.133333 0 34.133333-34.133333-38.4-38.4L128 473.6 119.466667 473.6l0-8.533333C119.466667 465.066667 119.466667 392.533333 119.466667 392.533333zM904.533333 392.533333l0 72.533333 0 8.533333L896 473.6l-290.133333 0L571.733333 512l34.133333 34.133333L896 546.133333l8.533333 0L904.533333 554.666667l0 76.8L1024 512C1024 512 904.533333 392.533333 904.533333 392.533333zM136.533333 563.2l0 85.333333 89.6 89.6 174.933333-174.933333L136.533333 563.2zM426.666667 563.2l-192 192-4.266667 4.266667-4.266667-4.266667-81.066667-81.066667 0 209.066667 209.066667 0-81.066667-81.066667-4.266667-4.266667 4.266667-4.266667 192-192 0-34.133333L426.666667 563.2 426.666667 563.2zM567.466667 563.2l0 38.4 192 192 4.266667 4.266667-4.266667 4.266667-81.066667 81.066667 213.333333 0 0-209.066667-85.333333 85.333333-4.266667 4.266667-4.266667-4.266667L597.333333 563.2 567.466667 563.2 567.466667 563.2zM622.933333 563.2l174.933333 174.933333 89.6-89.6 0-85.333333C887.466667 563.2 622.933333 563.2 622.933333 563.2zM512 571.733333l-34.133333 34.133333L477.866667 896l0 8.533333L469.333333 904.533333 388.266667 904.533333 512 1024l119.466667-119.466667-76.8 0-8.533333 0L546.133333 896l0-285.866667C550.4 610.133333 512 571.733333 512 571.733333zM460.8 622.933333l-174.933333 174.933333 85.333333 85.333333 85.333333 0L456.533333 622.933333 460.8 622.933333zM567.466667 627.2l0 260.266667 85.333333 0 85.333333-85.333333L567.466667 627.2z"/>
                      </svg>
                      <!-- Debian 图标 -->
                      <svg v-else-if="group.type === 'Debian'" viewBox="0 0 1024 1024" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M592.213333 541.013333c-17.066667 0 3.413333 8.533333 25.6 11.946667 5.973333-4.266667 11.52-9.386667 16.64-14.08a128 128 0 0 1-42.24 2.133333m91.306667-22.613333c9.813333-14.08 17.066667-29.44 20.053333-45.226667-2.56 11.52-8.533333 21.333333-14.08 31.146667-32 20.053333-2.986667-11.52 0-23.893333-34.133333 43.093333-4.693333 25.6-5.973333 37.973333m33.28-87.466667c2.133333-30.72-5.973333-21.333333-8.533333-9.386666 2.986667 1.706667 5.546667 21.333333 8.533333 9.386666M528.213333 13.226667c8.533333 1.706667 19.2 2.986667 17.92 5.12 9.813333-2.133333 11.946667-4.266667-18.346666-5.12m18.346666 5.12l-6.4 1.28 5.973334-0.426667V18.346667m282.88 424.106666c0.853333 27.306667-8.533333 40.533333-16.213334 64l-14.933333 7.68c-11.946667 23.04 1.28 14.933333-7.253333 33.28-18.773333 16.64-57.173333 52.053333-69.12 55.466667-8.533333 0 5.973333-10.666667 8.106666-14.506667-25.173333 17.066667-20.48 25.6-58.453333 36.266667l-1.28-2.56c-94.72 44.373333-226.133333-43.52-224-163.84-1.28 7.253333-2.986667 5.546667-5.12 8.533333a151.466667 151.466667 0 0 1 85.333333-149.333333 143.36 143.36 0 0 1 159.146667 20.48 142.506667 142.506667 0 0 0-116.053333-55.466667c-50.346667 0.426667-97.28 32.426667-113.066667 66.986667-25.6 16.213333-28.586667 62.72-39.68 70.826667-15.36 110.933333 28.16 158.72 101.546667 215.04 11.52 8.106667 3.413333 8.96 5.12 14.933333a200.533333 200.533333 0 0 1-65.28-49.493333c9.813333 14.08 20.053333 28.16 34.133333 38.826666-23.466667-7.68-54.186667-55.466667-63.146667-57.6 39.68 70.826667 161.28 124.586667 224.426667 98.133334a264.533333 264.533333 0 0 1-99.413333-11.946667c-14.08-6.826667-32.853333-21.76-29.866667-24.32a247.466667 247.466667 0 0 0 251.733333-35.84c18.773333-14.933333 39.68-40.106667 45.653334-40.533333-8.533333 13.653333 1.706667 6.826667-5.12 18.773333 18.773333-30.72-8.533333-12.8 19.626666-52.906667l10.24 14.08c-3.84-25.6 31.573333-56.32 28.16-96.426666 8.106667-12.8 8.533333 12.8 0 41.386666 12.373333-31.573333 3.413333-36.266667 6.4-62.293333 3.413333 8.533333 7.68 17.92 9.813334 26.88-7.68-29.866667 8.533333-51.2 11.946666-68.266667-3.84-2.133333-11.946667 12.8-13.653333-22.613333 0-15.786667 4.266667-8.533333 5.973333-11.946667-3.413333-2.133333-11.093333-13.653333-16.213333-36.693333 3.413333-5.546667 9.386667 14.08 14.506667 14.506667-3.413333-17.92-8.533333-32-8.533334-46.08-14.506667-29.013333-5.12 4.266667-17.066666-12.8-14.506667-46.506667 12.8-10.666667 14.506666-31.573334 23.04 32.853333 35.84 83.626667 41.813334 104.96-4.266667-25.6-11.946667-51.2-20.906667-75.093333 6.826667 2.986667-11.093333-52.906667 8.96-15.786667A333.653333 333.653333 0 0 0 755.2 68.266667c7.68 7.253333 17.92 16.64 14.08 17.92-32-19.2-26.453333-20.48-31.146667-28.586667-26.026667-10.666667-27.733333 0.853333-45.226666 0C643.413333 31.146667 634.026667 34.133333 588.8 17.066667l2.133333 9.813333c-32.853333-10.666667-38.4 4.266667-73.813333 0-2.133333-1.706667 11.52-5.973333 22.613333-7.68-31.573333 4.266667-29.866667-5.973333-61.013333 1.28 7.253333-5.546667 15.36-8.96 23.466667-13.653333-25.6 1.706667-61.44 14.933333-50.346667 2.986666C409.6 29.013333 334.933333 55.466667 293.12 94.72L291.84 85.333333c-19.2 23.04-83.626667 68.693333-88.746667 98.56l-5.546666 1.28c-9.813333 17.066667-16.213333 36.266667-24.32 53.76-12.8 22.186667-19.2 8.533333-17.066667 11.946667-25.6 52.053333-38.4 96-49.493333 132.266667 7.68 11.52 0 70.4 2.986666 117.76-12.8 232.96 163.84 459.52 356.693334 512 28.586667 9.813333 70.4 9.813333 106.24 10.666666-42.24-11.946667-47.786667-6.4-88.746667-20.906666-29.866667-13.653333-36.266667-29.866667-57.173333-48.213334l8.533333 14.933334c-41.386667-14.506667-24.32-17.92-58.026667-28.586667l8.96-11.52c-13.226667-1.28-35.413333-22.613333-41.386666-34.56l-14.506667 0.426667c-17.493333-21.333333-26.88-37.12-26.026667-49.493334l-4.693333 8.533334c-5.546667-8.96-64.853333-81.066667-34.133333-64.426667-5.546667-5.12-13.226667-8.533333-21.333334-23.466667l5.973334-7.253333c-14.933333-18.773333-27.306667-43.52-26.453334-51.2 8.533333 10.24 13.653333 12.8 19.2 14.08-37.546667-92.586667-39.68-5.12-68.266666-93.866667l6.4-0.853333c-4.266667-6.826667-7.68-14.506667-11.093334-21.76l2.56-25.6c-26.88-31.573333-7.68-132.266667-3.84-187.733333 2.986667-23.04 22.613333-46.933333 37.546667-84.48l-8.96-1.706667c17.066667-30.293333 99.84-122.453333 138.24-117.76 18.346667-23.466667-3.84 0-7.68-5.973333 40.96-42.24 53.76-29.866667 81.066667-37.546667 29.866667-17.066667-25.6 6.826667-11.52-6.4 51.2-12.8 36.266667-29.866667 103.253333-36.266667 6.826667 4.266667-16.64 5.973333-22.186667 11.093334 42.666667-20.906667 134.4-15.786667 194.56 11.52 69.546667 32.853333 147.626667 128.426667 150.613334 218.88l3.413333 0.853333c-1.706667 36.266667 5.546667 77.653333-7.253333 115.626667l8.533333-17.92m-421.12 122.453333l-2.133333 11.946667c11.093333 14.933333 20.053333 31.146667 34.133333 43.093333-10.24-20.053333-17.92-28.16-32-55.466667m26.453333-0.853333c-5.973333-6.4-9.386667-14.506667-13.226666-22.186667 3.413333 13.653333 11.093333 25.6 18.346666 37.546667l-5.12-15.36m466.773334-101.546667l-2.986667 6.4c-4.266667 32.426667-14.506667 64.426667-29.44 94.293334 17.066667-31.146667 27.733333-65.706667 32-100.693334M531.2 5.12c11.52-4.266667 28.16-2.133333 40.533333-5.12-15.786667 1.28-31.573333 2.133333-46.933333 4.266667l6.4 0.853333M128.426667 219.306667c2.986667 24.32-18.346667 34.133333 4.693333 17.92 12.8-28.16-4.693333-7.68-4.266667-17.92M101.546667 332.8c5.12-16.64 6.4-26.453333 8.533333-35.84-14.933333 18.773333-7.253333 22.613333-8.533333 35.413333"/>
                      </svg>
                      <!-- Fedora 图标 -->
                      <svg v-else-if="group.type === 'Fedora'" viewBox="0 0 1024 1024" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M513.504 96c-229.536-0.544-416 185.152-416.768 414.88L96 827.2a99.456 99.456 0 0 0 98.88 100.064h0.544l315.84 0.736c229.504 0.544 416.192-185.152 416.736-414.88C928.544 283.392 743.04 96.544 513.504 96z m91.872 160.384h0.384a134.08 134.08 0 0 1 31.296 3.552c17.312 2.816 30.752 18.432 30.752 37.184 0 24-23.04 43.104-46.944 37.056-42.432-9.152-102.08 22.336-102.24 85.088l-0.128 72.8a13.856 13.856 0 0 0 13.76 13.92h0.064l52.064 0.128c50.336 0.32 50.176 76.672-0.32 76.512l-66.016-0.128-0.16 86.752c-0.32 107.36-99.744 179.392-194.688 159.424-8.832 0-30.816-13.44-30.816-37.472a38.56 38.56 0 0 1 37.92-38.144c9.76 0 9.76 2.432 24.32 2.432a86.656 86.656 0 0 0 86.944-86.304v-0.256l0.128-72.8c0-6.976-7.008-13.952-13.824-13.952l-52.064-0.192c-50.496-0.16-50.304-76.512 0.192-76.352l66.016 0.192 0.096-86.752a162.976 162.976 0 0 1 163.264-162.688z m64 13.184a164.384 164.384 0 0 1 90.624 98.88l-84.128-84.64a40.896 40.896 0 0 0-6.496-14.24z m8.992 25.44l85.504 86.88c2.752 11.36 4.224 23.008 4.384 34.688l-95.744-97.024c3.52-7.296 5.856-14.208 5.856-22.112v-2.432z m-9.6 30.368l99.616 100.256c-0.384 8.64-1.6 17.248-3.584 25.696l-111.36-111.68c6.08-3.648 11.264-8.48 15.296-14.272z m12.224 51.872l82.24 82.688c-1.984 7.52-4.448 14.944-7.296 22.176l-63.936-64.32a89.92 89.92 0 0 0-11.008-40.544z m10.496 50.304l61.184 61.696a130.24 130.24 0 0 1-10.368 18.56l-56.352-56.672c3.136-7.488 4.992-15.488 5.536-23.584z m-8.32 30.56l55.456 55.648c-4.16 5.632-8.608 11.072-13.312 16.256l-54.08-54.272c4.64-5.44 8.64-11.328 11.968-17.6z m-16.736 22.752l54.08 54.272a195.84 195.84 0 0 1-15.648 13.92l-54.688-55.008c5.888-3.776 11.36-8.224 16.256-13.184z m-23.232 16.576l56.064 55.616a255.616 255.616 0 0 1-18.4 11.2l-58.88-59.52a76.8 76.8 0 0 0 21.216-7.296z m-303.648 8.992a77.76 77.76 0 0 0-11.936 17.696l-13.12-13.184a132.8 132.8 0 0 1 25.056-4.48z m-32.672 6.496l18.24 18.432-1.376 12.384c0.032 6.624 1.28 13.216 3.68 19.392L285.632 520.96c6.848-3.296 13.952-6.048 21.248-8.064z m-28.192 11.52l59.2 59.424c-7.584 1.408-14.976 3.84-21.888 7.328l-54.688-55.648c5.568-4.096 11.36-7.744 17.376-11.104z m353.696 0.928l41.792 42.112a115.68 115.68 0 0 1-21.248 8.192l-18.24-18.432 1.44-12.576a53.984 53.984 0 0 0-3.744-19.296z m-377.28 14.176l54.72 55.04c-5.856 3.84-11.296 8.256-16.256 13.152l-54.08-53.632c4.672-5.376 9.92-10.24 15.648-14.56z m-20.608 19.072l54.144 54.24a88.96 88.96 0 0 0-11.968 17.632l-55.488-55.552c4.32-5.888 8.832-11.2 13.312-16.32z m397.696 5.952l13.184 13.12a156.064 156.064 0 0 1-24.96 4.48 55.04 55.04 0 0 0 11.776-17.6z m-414.72 16.224l56.416 56.704a73.888 73.888 0 0 0-5.568 23.552L207.136 599.36a148.48 148.48 0 0 1 10.368-18.56z m-13.6 25.28l63.936 64.736c0.512 14.08 4.032 27.936 10.368 40.544l-81.6-82.88c2.176-7.552 4.512-14.848 7.296-22.4z m-8.992 30.496l111.872 112.32a58.56 58.56 0 0 0-15.36 13.472l-99.776-99.424c0.16-8.864 1.248-17.728 3.264-26.368z m-3.264 36.096l95.872 96.32a48.384 48.384 0 0 0-5.536 22.208v1.664L196.128 706.56a135.808 135.808 0 0 1-4.48-33.888z m8.192 47.584l84.128 84.544c1.312 5.12 3.52 9.92 6.496 14.272a164.608 164.608 0 0 1-90.624-98.816z"/>
                      </svg>
                      <!-- AlmaLinux 图标 -->
                      <svg v-else-if="group.type === 'AlmaLinux'" viewBox="0 0 1024 1024" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1023.839147 645.674667c3.370667 45.269333-28.501333 82.218667-72.106667 85.546666a76.8 76.8 0 0 1-82.261333-70.442666c-3.328-45.312 26.88-78.890667 72.149333-83.925334 43.648-3.328 78.890667 25.173333 82.218667 68.821334z m-538.581334 211.413333c-40.277333 0-73.813333 33.536-73.813333 77.184 0 48.64 31.872 78.848 80.512 78.848 38.570667-1.706667 72.149333-36.906667 72.149333-77.184 0-41.941333-38.570667-78.848-78.848-78.848z m45.269334-412.8c-1.664-36.906667-3.328-73.813333 3.413333-109.056 6.656-40.277333 13.397333-80.512 38.570667-114.090667 30.165333-41.941333 77.184-40.277333 102.357333 5.034667 13.397333 21.802667 18.432 45.312 20.096 70.485333 0 15.104 6.741333 18.432 20.138667 16.768 40.277333-6.698667 80.554667-6.698667 119.125333 8.405334 5.034667 1.664 10.069333 5.034667 16.810667 0 13.397333-11.776 16.768-70.485333 8.362666-85.589334-15.104-26.88-38.570667-23.466667-62.08-23.466666-26.837333 1.664-50.346667-6.741333-68.778666-28.586667-16.768-20.096-21.802667-45.226667-25.173334-70.4-1.706667-11.776-3.370667-21.845333-13.44-30.250667-23.466667-23.466667-77.184-18.432-105.685333 5.034667-109.056 87.253333-127.530667 233.258667-65.450667 349.013333 1.706667 5.034667 5.034667 10.069333 11.733334 6.698667z m340.650666 156.074667c15.104-21.802667 36.906667-31.872 60.373334-41.941334a41.514667 41.514667 0 0 0 25.173333-20.138666c15.104-28.544-3.328-77.226667-31.872-100.693334-110.72-85.589333-253.354667-68.778667-349.013333 18.474667-5.034667 3.328-6.698667 8.362667-3.328 13.397333 33.536-10.069333 68.778667-20.138667 105.685333-21.76 38.613333-3.413333 78.848-6.741333 117.461333 10.026667 48.64 20.138667 57.045333 65.450667 20.138667 100.693333-16.768 16.768-38.613333 28.501333-62.08 35.2-13.44 3.413333-15.104 10.069333-10.069333 23.509334 15.104 36.906667 25.173333 75.52 20.138666 117.461333-1.706667 6.698667-3.370667 11.733333 3.328 16.768 15.104 10.069333 72.149333 0 83.925334-11.733333 21.802667-20.138667 13.397333-43.648 8.362666-65.450667-6.698667-26.88-3.328-52.010667 11.776-73.813333z m-307.072-87.253334c-5.034667-3.370667-8.405333-5.034667-13.44 0 20.138667 30.208 38.613333 62.08 53.717334 95.616 13.397333 36.949333 28.501333 73.813333 23.466666 115.797334-5.034667 50.346667-46.933333 72.106667-90.581333 46.976-21.802667-11.733333-38.613333-28.544-52.053333-48.64-8.362667-11.776-16.768-11.776-26.837334-3.413334-31.872 26.88-65.408 47.018667-107.349333 53.76-6.741333 0-13.44 0-16.810667 6.698667-5.034667 16.768 20.138667 68.778667 35.242667 77.184 25.173333 15.104 45.312 0 65.450667-11.776 23.466667-13.397333 46.976-18.432 73.813333-10.069333 25.173333 8.405333 41.941333 26.88 57.045333 47.018666 6.741333 8.362667 13.44 15.061333 26.88 18.432 31.872 8.405333 75.52-25.173333 88.917334-58.709333 50.346667-129.194667-6.698667-261.76-117.461334-328.874667z m-109.056 104.021334c20.138667-28.544 35.242667-60.416 41.941334-93.952-6.698667-1.706667-8.405333 1.706667-13.44 3.328-38.570667 40.277333-78.848 78.890667-130.858667 105.728-20.138667 10.069333-41.941333 18.474667-65.450667 18.474666-36.906667 0-58.709333-23.509333-55.381333-60.416a124.586667 124.586667 0 0 1 33.578667-78.890666c10.069333-11.733333 10.069333-18.432-1.706667-28.501334-33.536-23.466667-63.744-52.053333-78.848-90.624-3.328-11.733333-11.733333-11.733333-21.76-6.698666a183.168 183.168 0 0 0-18.517333 10.069333c-43.605333 26.88-48.64 60.416-11.733334 97.28 26.88 26.88 40.277333 57.088 30.208 93.994667-5.034667 18.474667-15.104 31.872-26.88 46.976a40.533333 40.533333 0 0 0-10.026666 33.578666c3.370667 31.872 35.242667 63.744 73.813333 67.114667 107.392 10.069333 194.645333-26.88 255.061333-117.461333z m-199.68-219.818667c58.709333 50.346667 130.858667 62.08 206.378667 58.752 6.698667 0 13.44 0 15.104-5.034667 1.706667-8.405333-6.698667-8.405333-11.733333-10.069333-35.242667-15.104-72.149333-26.88-104.021334-48.64S292.27648 352 278.879147 310.016c-10.069333-31.872 3.328-60.373333 35.2-70.442667 28.544-10.069333 57.045333-10.069333 83.925333 0 16.768 6.698667 23.466667 3.328 26.837333-15.104 5.034667-31.872 15.104-62.08 35.242667-88.96 23.466667-33.536 23.466667-36.906667-15.104-58.709333-1.706667 0-1.706667-1.706667-3.370667-1.706667-36.906667-20.096-65.450667-8.362667-78.848 30.250667-20.138667 58.709333-58.752 80.512-120.832 68.778667a156.032 156.032 0 0 0-20.138666-3.370667c-31.872 5.034667-50.346667 23.466667-55.338667 48.64-6.741333 77.226667 33.536 132.565333 88.917333 177.92z m-98.986666 156.074667c-3.370667-40.277333-43.648-70.485333-87.253334-65.450667-38.613333 3.370667-72.149333 43.605333-68.821333 83.882667 3.413333 41.941333 43.648 75.52 83.925333 70.485333 48.64-3.370667 73.813333-50.346667 72.106667-88.917333z m647.68-354.048c40.234667-3.370667 73.813333-41.941333 70.442666-82.218667-3.328-41.941333-43.605333-75.52-85.546666-72.149333-43.648 3.370667-73.813333 41.941333-70.485334 83.882666s41.941333 73.813333 85.589334 70.485334z m-523.52-35.242667c45.312-6.698667 75.52-43.648 70.485333-87.253333C345.993813 38.272 305.71648 6.357333 263.775147 11.392c-45.312 5.034667-72.149333 40.277333-67.114667 88.96 5.034667 36.906667 45.269333 68.778667 83.882667 63.744z"/>
                      </svg>
                      <!-- Windows 图标 -->
                      <svg v-else-if="group.type === 'Windows'" viewBox="0 0 1024 1024" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0 147.157333L416 89.6v403.242667H0m467.157333-409.685334L1024 0v486.4H467.157333M0 537.6h416v403.242667L0 883.157333M467.157333 537.6H1024V1024l-550.4-76.842667"/>
                      </svg>
                      <!-- 默认 Linux 图标 -->
                      <svg v-else viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2V7zm0 8h2v2h-2v-2z"/>
                      </svg>
                    </div>
                    <div class="hlwidc-os-type-info">
                      <h4 class="hlwidc-os-type-name">{{ group.type }}</h4>
                      <p class="hlwidc-os-type-count">{{ group.images.length }} 个版本</p>
                    </div>
                  </div>
                </label>
              </div>
              
              <!-- 选择具体镜像 -->
              <div v-if="selectedOsType" class="hlwidc-image-selection">
                <div class="hlwidc-image-list">
                  <label 
                    v-for="image in getSelectedOsImages()" 
                    :key="image.id" 
                    class="hlwidc-image-option"
                    :class="{ 'selected': selectedImageId === image.id }"
                  >
                    <input 
                      type="radio" 
                      :value="image.id" 
                      v-model="selectedImageId"
                      name="imageSelect"
                      class="hlwidc-image-radio"
                    />
                    <span class="hlwidc-image-name">{{ image.name }}</span>
                  </label>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="hlwidc-modal-footer">
        <button @click="closeModal" class="hlwidc-modal-btn hlwidc-cancel-btn">
          取消
        </button>
        <button @click="confirmRebuild" class="hlwidc-modal-btn hlwidc-confirm-btn" :disabled="loading || !selectedImageId">
          {{ loading ? '重装中...' : '确定重装' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import type { ImageGroup } from '@/services/panel'

const props = defineProps<{
  show: boolean
  imageGroups: ImageGroup[]
  imageLoading: boolean
  imageError: string
  loading: boolean
  currentImage?: string
}>()

const emit = defineEmits<{
  close: []
  loadImages: []
  confirm: [imageId: number]
}>()

const selectedOsType = ref<string | null>(null)
const selectedImageId = ref<number | null>(null)

// 获取选中操作系统的镜像列表
const getSelectedOsImages = () => {
  if (!selectedOsType.value) return []
  const group = props.imageGroups.find(g => g.type === selectedOsType.value)
  return group ? group.images : []
}

// 设置默认选择
const setDefaultSelection = () => {
  if (!props.currentImage || !props.imageGroups.length) {
    // 如果没有当前镜像或镜像组为空，选择第一个类型和第一个镜像
    if (props.imageGroups.length > 0) {
      const firstGroup = props.imageGroups[0]
      if (firstGroup) {
        selectedOsType.value = firstGroup.type
        if (firstGroup.images.length > 0 && firstGroup.images[0]) {
          selectedImageId.value = firstGroup.images[0].id
        }
      }
    }
    return
  }

  // 根据当前镜像名称匹配系统类型
  const currentImageName = props.currentImage.toLowerCase()
  let matchedGroup = null
  let matchedImage = null

  for (const group of props.imageGroups) {
    for (const image of group.images) {
      if (image.name.toLowerCase().includes(currentImageName) || 
          currentImageName.includes(group.type.toLowerCase())) {
        matchedGroup = group
        matchedImage = image
        break
      }
    }
    if (matchedGroup) break
  }

  if (matchedGroup && matchedImage) {
    selectedOsType.value = matchedGroup.type
    selectedImageId.value = matchedImage.id
  } else {
    // 如果没有匹配到，选择第一个类型和第一个镜像
    if (props.imageGroups.length > 0) {
      const firstGroup = props.imageGroups[0]
      if (firstGroup) {
        selectedOsType.value = firstGroup.type
        if (firstGroup.images.length > 0 && firstGroup.images[0]) {
          selectedImageId.value = firstGroup.images[0].id
        }
      }
    }
  }
}

// 监听 imageGroups 变化，设置默认选择
watch(() => props.imageGroups, () => {
  if (props.imageGroups.length > 0) {
    setDefaultSelection()
  }
}, { immediate: true })

const confirmRebuild = () => {
  if (!selectedImageId.value) return
  emit('confirm', selectedImageId.value)
}

const closeModal = () => {
  selectedOsType.value = null
  selectedImageId.value = null
  emit('close')
}
</script>

<style scoped>
/* 弹窗样式 */
.hlwidc-modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 99999;
  padding: 20px;
}

.hlwidc-modal-content {
  background: white;
  border-radius: 12px;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  max-width: 400px;
  width: 100%;
  max-height: 90vh;
  overflow: hidden;
  animation: modalSlideIn 0.3s ease-out;
  position: relative;
  z-index: 100000;
}

@keyframes modalSlideIn {
  from {
    opacity: 0;
    transform: scale(0.9) translateY(-20px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

/* 重装系统弹窗样式 */
.hlwidc-rebuild-modal {
  max-width: 600px;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

.hlwidc-rebuild-modal .hlwidc-modal-header {
  background: linear-gradient(135deg, #f59e0b, #d97706);
  color: white;
  border-bottom: none;
  padding: 24px 28px 20px;
}

.hlwidc-rebuild-modal .hlwidc-modal-title {
  color: white;
  font-size: 20px;
  font-weight: 700;
  display: flex;
  align-items: center;
  gap: 10px;
}

.hlwidc-rebuild-modal .hlwidc-modal-title::before {
  content: '';
  display: inline-block;
  width: 24px;
  height: 24px;
  background-image: url("data:image/svg+xml,%3Csvg fill='none' stroke='white' viewBox='0 0 24 24' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15'%3E%3C/path%3E%3C/svg%3E");
  background-size: contain;
  background-repeat: no-repeat;
  filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
}

.hlwidc-rebuild-modal .hlwidc-modal-close {
  color: white;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 8px;
  width: 36px;
  height: 36px;
  font-size: 20px;
  font-weight: 300;
}

.hlwidc-rebuild-modal .hlwidc-modal-close:hover {
  background: rgba(255, 255, 255, 0.2);
  transform: scale(1.05);
}

.hlwidc-rebuild-form {
  padding: 0;
}

.hlwidc-loading-images {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  padding: 40px 20px;
  color: #6b7280;
  font-size: 14px;
}

.hlwidc-error-images {
  text-align: center;
  padding: 40px 20px;
  color: #dc2626;
}

.hlwidc-error-images p {
  margin: 0 0 16px 0;
  font-size: 14px;
}

.hlwidc-retry-btn {
  background-color: #3b82f6;
  color: white;
  border: none;
  padding: 12px 24px;
  border-radius: 8px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 500;
  margin-top: 16px;
}

.hlwidc-retry-btn:hover {
  background-color: #2563eb;
}

.hlwidc-loading-spinner {
  width: 20px;
  height: 20px;
  border: 2px solid #e5e7eb;
  border-top: 2px solid #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* 操作系统类型选择 */
.hlwidc-os-type-selection {
  margin-top: 16px;
}

.hlwidc-os-type-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
  gap: 8px;
  margin-bottom: 16px;
}

.hlwidc-os-type-option {
  display: flex;
  align-items: center;
  padding: 10px;
  background: white;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.hlwidc-os-type-option::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 3px;
  background: linear-gradient(90deg, #f59e0b, #d97706);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.hlwidc-os-type-option:hover {
  border-color: #f59e0b;
  background: #fffbeb;
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(245, 158, 11, 0.15);
}

.hlwidc-os-type-option:hover::before {
  opacity: 1;
}

.hlwidc-os-type-option.selected {
  border-color: #f59e0b;
  background: linear-gradient(135deg, #fffbeb, #fef3c7);
  box-shadow: 0 8px 25px rgba(245, 158, 11, 0.2);
}

.hlwidc-os-type-option.selected::before {
  opacity: 1;
}

.hlwidc-os-type-radio {
  position: absolute;
  opacity: 0;
  pointer-events: none;
}

.hlwidc-os-type-content {
  display: flex;
  align-items: center;
  flex: 1;
  width: 100%;
}

.hlwidc-os-type-icon {
  width: 28px;
  height: 28px;
  background: linear-gradient(135deg, #f59e0b, #d97706);
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 8px;
  flex-shrink: 0;
  transition: all 0.3s ease;
}

.hlwidc-os-type-icon svg {
  width: 14px;
  height: 14px;
  color: white;
  transition: all 0.3s ease;
}

.hlwidc-os-type-option:hover .hlwidc-os-type-icon {
  transform: scale(1.1);
  box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
}

.hlwidc-os-type-info {
  flex: 1;
}

.hlwidc-os-type-name {
  margin: 0 0 2px 0;
  font-size: 12px;
  font-weight: 600;
  color: #1f2937;
  line-height: 1.3;
}

.hlwidc-os-type-count {
  margin: 0;
  font-size: 10px;
  color: #6b7280;
  font-weight: 500;
  line-height: 1.3;
}

/* 镜像选择区域 */
.hlwidc-image-selection {
  margin-top: 24px;
  padding-top: 24px;
  border-top: 2px solid #e5e7eb;
}

.hlwidc-image-list {
  padding: 12px 20px 16px;
}

.hlwidc-image-option {
  display: flex;
  align-items: center;
  padding: 12px 16px;
  margin: 8px 0;
  background: white;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
  position: relative;
}

.hlwidc-image-option:hover {
  border-color: #f59e0b;
  background: #fffbeb;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(245, 158, 11, 0.15);
}

.hlwidc-image-option.selected {
  border-color: #f59e0b;
  background: linear-gradient(135deg, #fffbeb, #fef3c7);
  box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);
}

.hlwidc-image-option.selected::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 3px;
  background: linear-gradient(90deg, #f59e0b, #d97706);
  border-radius: 6px 6px 0 0;
}

.hlwidc-image-radio {
  margin-right: 12px;
  width: 16px;
  height: 16px;
  accent-color: #f59e0b;
}

.hlwidc-image-name {
  font-size: 14px;
  font-weight: 500;
  color: #374151;
  flex: 1;
}

.hlwidc-modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 24px 16px;
  border-bottom: 1px solid #e5e7eb;
}

.hlwidc-modal-title {
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
  margin: 0;
}

.hlwidc-modal-close {
  background: none;
  border: none;
  font-size: 24px;
  color: #6b7280;
  cursor: pointer;
  padding: 0;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 6px;
  transition: all 0.2s;
}

.hlwidc-modal-close:hover {
  background-color: #f3f4f6;
  color: #374151;
}

.hlwidc-modal-body {
  padding: 20px 24px;
}

.hlwidc-modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  padding: 16px 24px 20px;
  border-top: 1px solid #e5e7eb;
}

.hlwidc-modal-btn {
  padding: 8px 16px;
  border: none;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  min-width: 80px;
}

.hlwidc-confirm-btn {
  background-color: #3b82f6;
  color: white;
}

.hlwidc-confirm-btn:hover {
  background-color: #2563eb;
}

.hlwidc-cancel-btn {
  background-color: #f3f4f6;
  color: #374151;
  border: 1px solid #d1d5db;
}

.hlwidc-cancel-btn:hover {
  background-color: #e5e7eb;
}

/* 响应式设计 */
@media (max-width: 768px) {
  .hlwidc-modal-overlay {
    padding: 16px;
  }
  
  .hlwidc-modal-content {
    max-width: none;
    width: 100%;
  }
  
  .hlwidc-rebuild-modal {
    max-width: none;
    width: 100%;
    border-radius: 12px;
  }
  
  .hlwidc-rebuild-modal .hlwidc-modal-header {
    padding: 20px 24px 16px;
  }
  
  .hlwidc-rebuild-modal .hlwidc-modal-title {
    font-size: 18px;
  }
  
  .hlwidc-os-type-grid {
    grid-template-columns: 1fr;
    gap: 10px;
  }
  
  .hlwidc-os-type-option {
    padding: 14px;
  }
  
  .hlwidc-os-type-icon {
    width: 32px;
    height: 32px;
    margin-right: 10px;
  }
  
  .hlwidc-os-type-icon svg {
    width: 16px;
    height: 16px;
  }
  
  .hlwidc-os-type-name {
    font-size: 13px;
  }
  
  .hlwidc-os-type-count {
    font-size: 11px;
  }
  
  .hlwidc-image-selection {
    margin-top: 20px;
    padding-top: 20px;
  }
  
  .hlwidc-modal-header {
    padding: 16px 20px 12px;
  }
  
  .hlwidc-modal-body {
    padding: 16px 20px;
  }
  
  .hlwidc-modal-footer {
    padding: 12px 20px 16px;
    flex-direction: column;
    gap: 8px;
  }
  
  .hlwidc-modal-btn {
    width: 100%;
    padding: 12px 16px;
  }
}
</style>
