<div class="m-auto md:p-10 w-full h-full relative">

    <div class="relative h-full  md:h-[600px] w-full md:w-96 m-auto">

        @for ($i = 0; $i < 4; $i++) 
        <div 
            @swipedright.window="console.log('right')"
            @swipedleft.window="console.log('left')" 
            @swipedup.window="console.log('up')"
            x-data="{
                profile:false,
                isSwiping:false,
                swipingLeft:false,
                swipingRight:false,
                swipingUp:false,
                swipeRight: function(){

                    moveOutWidth= document.body.clientWidth *1.5;

                    $el.style.transform= 'translate(' +moveOutWidth+ 'px, -100px ) rotate(-30deg)';

                    setTimeout(()=>{

                        $el.remove();
                    },300);

                    {{-- dispatch --}}
                    $dispatch('swipedright');


                },
                swipeLeft: function(){

                    moveOutWidth= document.body.clientWidth *1.5;

                    $el.style.transform= 'translate(' + -moveOutWidth+ 'px, -100px ) rotate(-30deg)';

                    setTimeout(()=>{
                        $el.remove();
                    },300);

                    {{-- dispatch --}}
                    $dispatch('swipedleft');


                },
                swipeUp: function(){

                    moveOutWidth= document.body.clientWidth *1.5;

                    $el.style.transform= 'translate(0px,'+ -moveOutWidth + 'px) rotate(-20deg)' ;

                    setTimeout(()=>{
                        $el.remove();
                    },300);

                    {{-- dispatch --}}
                    $dispatch('swipedup');


                }
               }" 
            x-init="
                
                element= $el;

                {{-- initialize hammer.js --}}
                var hammertime= new Hammer(element);

                {{-- lets pan support all directions --}}
                hammertime.get('pan').set({
                    direction: Hammer.DIRECTION_ALL,
                    touchAction:'pan'
                });

                {{-- ON pan --}}
                hammertime.on('pan',function(event){

                    isSwiping=true;

                    if(event.deltaX===0) return;
                    if(event.center.x=== 0 && event.center.y===0) return;


                    {{-- Swiped right --}}
                    if(event.deltaX > 20){
                        swipingRight=true;//true
                        swipingLeft=false;
                        swipingUp=false;
                    }

                    {{-- Swipedleft --}}
                    else if(event.deltaX < -20){

                        swipingRight=false;
                        swipingLeft=true;//true
                        swipingUp=false;

                    }

                    {{-- Suuper like Swiped Up --}}
                    else if(event.deltaY < -50 && Math.abs(event.deltaX ) < 20){
                        swipingRight=false;
                        swipingLeft=false;
                        swipingUp=true;//true
                    }

                    {{-- Rotate --}}

                    var rotate= event.deltaX/10;

                    {{-- Apply transformation to rotate only in X direction 
                        in somewhat Clockwise and Anit clokwise
                        --}}

                    event.target.style.transform= 'translate('+ event.deltaX + 'px,' + event.deltaY +'px) rotate(' +rotate+ 'deg';

                });

                hammertime.on('panend',function(event){

                    {{-- reset states --}}
                    isSwiping=false;
                    swipingLeft=false;
                    swipingRight=false;
                    swipingUp=false;

                    {{-- set threshold --}}
                    var horizontalThreshold=200;
                    var verticalThreshold =200;

                    {{-- velocity threshold --}}
                    var velocityXThreshold= 0.5;
                    var velocityYThreshold= 0.5;

                    {{-- Determine keep --}}

                    var keep= Math.abs(event.deltaX) < horizontalThreshold && Math.abs(event.velocityX)<velocityXThreshold &&
                            Math.abs(event.deltaY) < verticalThreshold && Math.abs(event.velocityY)< velocityYThreshold;

                    
                    console.log('keep '+ keep);
                    console.log('event.deltaX ' + event.deltaX);

                    if(keep){

                        {{-- adjust the duration and timing as needed --}}

                        event.target.style.transition='transform 0.3s ease-in-out';
                        event.target.style.transform='';
                        $el.style.transform='';

                        {{-- clear the trsntion --}}

                        setTimeout(()=>{
                            event.target.style.transition='';
                            event.target.style.transform='';
                            $el.style.transform='';
                        },300);//use same as duration 


                    }
                    else{

                        var moveOutWidth= document.body.clientWidth;
                        var moveOutHeight= document.body.clientHeight;


                        {{-- Decie to push let right or up --}}

                        {{-- Swipe right --}}

                        if(event.deltaX >20){

                            event.target.style.transform= 'translate(' + moveOutWidth +'px, 10px)';
                            $dispatch('swipedright');

                        }

                        {{-- swipeLeft --}}
                        else if(event.deltaX < -20){

                            event.target.style.transform= 'translate(' + -moveOutWidth +'px, 10px)';
                            $dispatch('swipedleft');

                        }

                        else if(event.deltaY  <- 50 && Math.abs(event.deltaX)<20 ){

                            event.target.style.transform= 'translate(0px,' +  -moveOutHeight + 'px)';
                            $dispatch('swipedup');

                        }

                        event.target.remove();
                        $el.remove();

                    }




                });

            " 
            :class="{'transform-none cursor-grab':isSwiping}"
            class="absolute inset-0 m-auto transform ease-in-out duration-300 rounded-xl  cursor-pointer z-50 ">

            {{-- swipe card --}}
            <div  
              x-show="!profile"
              x-transition.duration.150ms.origin.bottom
             class="relative overflow-hidden w-full h-full rounded-xl bg-cover bg-white">

                @php
                $slides=['https://source.unsplash.com/500x500?face-woman-'.rand(1,20),'https://source.unsplash.com/500x500?face-woman-'.rand(1,20),'https://source.unsplash.com/500x500?face-woman-'.rand(1,20),]
                @endphp

                {{-- Carousel section --}}
                <section x-data="{activeSlide:1, slides:@js($slides)}">

                    {{-- Slides --}}
                    <template x-for="(image,index) in slides" :key="index">
                        <img x-show="activeSlide===index + 1" :src="image" alt="image"
                            class="absolute inset-0 pointer-events-none w-full h-full object-cover">

                    </template>

                    {{-- pagination --}}
                    <div 
                    draggable="true"
                    :class="{'hidden':slides.length==1}"
                        class="absolute top-1 inset-x-0 z-10 w-full flex items-center justify-center">

                        <template x-for="(image,index) in slides" :key="index">

                            <button @click="activeSlide=index+1"
                                :class="{'bg-white':activeSlide===index +1,'bg-gray-500':activeSlide !== index+1}"
                                class="flex-1 w-4 h-2 mx-1 rounded-full overflow-hidden">

                            </button>
                        </template>


                    </div>

                    {{-- Prev button --}}
                    <button draggable="true" :class="{'hidden':slides.length==1}"
                        @click="activeSlide = activeSlide ===1? slides.length:activeSlide-1"
                        class="absolute left-2 top-1/2 my-auto ">

                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3"
                            stroke="currentColor" class="w-6 h-6 text-white">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                        </svg>


                    </button>

                    {{-- Next button --}}
                    <button draggable="true" :class="{'hidden':slides.length==1}"
                        @click="activeSlide = activeSlide === slides.length ? 1 : activeSlide +1"
                        class="absolute right-2 top-1/2 my-auto ">

                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-6 h-6 text-white">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                          </svg>


                    </button>

                </section>

                {{-- Swiper indicators --}}
                <div class="pointer-events-none">
                    <span x-cloak :class="{'invisible':!swipingRight}"
                        class="border-2 rounded-md p-1 px-2 border-green-500 text-green-500 text-4xl capitalize font-extrabold top-10 left-5 -rotate-12 absolute z-5 ">
                        LIKE
                    </span>
                    <span x-cloak :class="{'invisible':!swipingLeft}"
                        class="border-2 rounded-md p-1 px-2 border-red-500 text-red-500 text-4xl capitalize font-extrabold top-10 right-5 rotate-12 absolute z-5 ">
                        NOPE
                    </span>
                    <span x-cloak :class="{'invisible':!swipingUp}"
                        class="border-2 rounded-md p-1 px-2 border-green-500 text-green-500 text-4xl capitalize font-extrabold   bottom-48 max-w-fit inset-x-0 mx-auto  -rotate-12 absolute z-5 ">
                        SUPER LIKE
                    </span>

                </div>

                {{-- information and actions --}}
                <section
                    class="absolute inset-x-0 bottom-0 inset-y-1/2 py-2 bg-gradient-to-t from-black to-black/0 pointer-events-none">

                    <div class=" flex flex-col h-full gap-2.5 mt-auto p-5 text-white">
                        {{-- personal details --}}
                        <div class="grid grid-cols-12 items-center">

                            <div class="col-span-10">
                                <h4 class="font-bold text-3xl">
                                    {{fake()->name}}
                                </h4>

                                <p class="text-lg line-clamp-3">
                                    Lorem ipsum dolor sit, amet consectetur adipisicing elit. Vel tempore cupiditate
                                    voluptate quia, itaque consectetur, voluptates quod repellendus facilis ut quo
                                    amet accusantium quam vitae officiis, rerum adipisci reiciendis cumque.
                                </p>

                            </div>

                            {{-- Open profile --}}
                            <div class="col-span-2 justify-end flex pointer-events-auto">

                                <button @click="profile =!profile " draggable="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                        class="w-6 h-6 text-white">
                                        <path fill-rule="evenodd"
                                            d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 0 1 .67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 1 1-.671-1.34l.041-.022ZM12 9a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z"
                                            clip-rule="evenodd" />
                                    </svg>


                                </button>
                            </div>



                        </div>


                        {{-- Actions --}}

                        <div class="grid grid-cols-5 gap-2 items-center mt-auto">

                            {{-- rewind --}}
                            <div>
                                <button draggable="false"
                                    class="rounded-full border-2 pointer-events-auto group border-yellow-600 p-3 shrink-0 max-w-fit flex items-center text-yellow-600  ">

                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                        class="w-9 h-9 shrink-0 m-auto group-hover:scale-105 transition-transform strok-2 stroke-current ">
                                        <path fill-rule="evenodd"
                                            d="M9.53 2.47a.75.75 0 0 1 0 1.06L4.81 8.25H15a6.75 6.75 0 0 1 0 13.5h-3a.75.75 0 0 1 0-1.5h3a5.25 5.25 0 1 0 0-10.5H4.81l4.72 4.72a.75.75 0 1 1-1.06 1.06l-6-6a.75.75 0 0 1 0-1.06l6-6a.75.75 0 0 1 1.06 0Z"
                                            clip-rule="evenodd" />
                                    </svg>



                                </button>

                            </div>

                            {{-- swipe left --}}
                            <div>
                                <button draggable="true" @click="swipeLeft()"
                                    class="rounded-full border-2 pointer-events-auto group border-red-600 p-2 shrink-0 max-w-fit flex items-center text-red-600  ">

                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                        stroke-width="3" stroke="currentColor"
                                        class="w-10 h-10 shrink-0 m-auto group-hover:scale-105 transition-transform 2">
                                        <path fill-rule="evenodd"
                                            d="M5.47 5.47a.75.75 0 0 1 1.06 0L12 10.94l5.47-5.47a.75.75 0 1 1 1.06 1.06L13.06 12l5.47 5.47a.75.75 0 1 1-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 0 1-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 0 1 0-1.06Z"
                                            clip-rule="evenodd" />
                                    </svg>

                                </button>

                            </div>

                            {{-- Super Like --}}
                            <div>
                                <button draggable="true" @click="swipeUp()"
                                    class="rounded-full border-2 pointer-events-auto group border-blue-600 p-1.5 shrink-0 max-w-fit flex items-center text-blue-600 scale-95 ">

                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                        class="w-11 h-11 shrink-0 m-auto group-hover:scale-105 transition-transform ">
                                        <path fill-rule="evenodd"
                                            d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z"
                                            clip-rule="evenodd" />
                                    </svg>


                                </button>

                            </div>

                            {{-- Swipe Right --}}
                            <div>
                                <button draggable="true" @click="swipeRight()"
                                    class="rounded-full border-2 pointer-events-auto group border-green-600 p-2 shrink-0 max-w-fit flex items-center text-green-600   ">


                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                        class="w-10 h-10 shrink-0 m-auto group-hover:scale-105 transition-transform ">
                                        <path
                                            d="m11.645 20.91-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.004-.003.001a.752.752 0 0 1-.704 0l-.003-.001Z" />
                                    </svg>


                                </button>

                            </div>

                            {{-- Boost --}}
                            <div>
                                <button draggable="true"
                                    class="rounded-full border-2 pointer-events-auto group border-purple-600 p-2 shrink-0 max-w-fit flex items-center text-purple-600   ">



                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                        class="w-10 h-10 shrink-0 m-auto group-hover:scale-105 transition-transform ">
                                        <path fill-rule="evenodd"
                                            d="M14.615 1.595a.75.75 0 0 1 .359.852L12.982 9.75h7.268a.75.75 0 0 1 .548 1.262l-10.5 11.25a.75.75 0 0 1-1.272-.71l1.992-7.302H3.75a.75.75 0 0 1-.548-1.262l10.5-11.25a.75.75 0 0 1 .913-.143Z"
                                            clip-rule="evenodd" />
                                    </svg>



                                </button>

                            </div>



                        </div>

                    </div>

                </section>

            </div>


            {{-- profile card --}}

            <div
             x-cloak
             x-show="profile"
             x-transition.duration.150ms.origin.top
             draggable="true"
             style="contain: content"
             class="absolute inset-0 overflow-y-auto overflow-hidden overscroll-contain border rounded-xl bg-white space-y-4">


             @php
             $slides=['https://source.unsplash.com/500x500?face-woman-'.rand(1,20),'https://source.unsplash.com/500x500?face-woman-'.rand(1,20),'https://source.unsplash.com/500x500?face-woman-'.rand(1,20),]
             @endphp

             {{-- Carousel section --}}
             <section class="relative  h-96" x-data="{activeSlide:1, slides:@js($slides)}">

                 {{-- Slides --}}
                 <template x-for="(image,index) in slides" :key="index">
                     <img x-show="activeSlide===index + 1" :src="image" alt="image"
                         class="absolute inset-0 pointer-events-none w-full h-full object-cover">

                 </template>

                 {{-- pagination --}}
                 <div 
                 draggable="true"
                 :class="{'hidden':slides.length==1}"
                     class="absolute top-1 inset-x-0 z-10 w-full flex items-center justify-center">

                     <template x-for="(image,index) in slides" :key="index">

                         <button @click="activeSlide=index+1"
                             :class="{'bg-white':activeSlide===index +1,'bg-gray-500':activeSlide !== index+1}"
                             class="flex-1 w-4 h-2 mx-1 rounded-full overflow-hidden">

                         </button>
                     </template>


                 </div>

                 {{-- Prev button --}}
                 <button draggable="true" :class="{'hidden':slides.length==1}"
                     @click="activeSlide = activeSlide ===1? slides.length:activeSlide-1"
                     class="absolute left-2 top-1/2 my-auto ">

                     <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3"
                         stroke="currentColor" class="w-6 h-6 text-white">
                         <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                     </svg>


                 </button>

                 {{-- Next button --}}
                 <button draggable="true" :class="{'hidden':slides.length==1}"
                     @click="activeSlide = activeSlide === slides.length ? 1 : activeSlide +1"
                     class="absolute right-2 top-1/2 my-auto ">

                     <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-6 h-6 text-white">
                         <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                       </svg>
                 </button>

                 {{-- close profile button --}}

                 <button @click="profile=false" class="absolute -bottom-4 right-3 bg-meetcast p-3 hover:scale-110 transition-transform rounded-full max-w-fit max-h-fit text-white ">

                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5 12 21m0 0-7.5-7.5M12 21V3" />
                      </svg>

                 </button>


             </section>


             {{-- profile information --}}

             <section class="grid gap-4 p-3">

                <div class="flex items-center text-3xl gap-3 text-wrap">
                    <h3 class="font-bold"> {{fake()->name}} </h3>
                    <span class="font-semibold text-gray-800">
                        22
                    </span>
                </div>

                {{-- about --}}

                <ul>
                    <li class="items-center text-gray-600 text-lg">
                        Software developer
                    </li>
                    <li class="items-center text-gray-600 text-lg">
                        186 cm
                    </li>
                    <li class="items-center text-gray-600 text-lg">
                        Lives in Spain
                    </li>
                </ul>

                 <hr class="-mx-2.5">

                 {{-- bio --}}

                 <p class="text-gray-600"> Lorem ipsum dolor sit amet consectetur adipisicing elit. Ad odio deleniti quas maxime debitis eum ullam totam ratione sapiente aspernatur, adipisci, magnam animi a? Fuga exercitationem in voluptatem consectetur eum?</p>

                 {{-- Relatioship goals --}}
                 <div class="rounded-xl bg-green-200 h-24 px-4 py-2 max-w-fit flex gap-4 items-center">

                    <div class="text-3xl"> 👋 </div>
                    <div class="grid w-4/5">
                        <span class="font-bold text-sm text-green-800">Looking for  </span>
                        <span class="text-lg text-green-800"> New friends </span>

                    </div>
                 </div>

                 {{-- More information --}}
                 
                 <section class="divide-y space-y-2">

                    <div class="spacey-y-3 py-2">
                        <h3 class="font-bold text-xl py-2"> Languages i know </h3>
                         <ul class="flex flex-wrap gap-3">
                            <li class="border border-gray-500 rounded-2xl text-sm px-2.5 p-1.5">German</li>
                            <li class="border border-gray-500 rounded-2xl text-sm px-2.5 p-1.5">Turkish</li>
                            <li class="border border-gray-500 rounded-2xl text-sm px-2.5 p-1.5">English</li>
                         </ul>
                    </div>

                    <div class="spacey-y-3 py-2">
                        <h3 class="font-bold text-xl py-2"> Basics </h3>
                         <ul class="flex flex-wrap gap-3">
                            <li class="border border-gray-500 rounded-2xl text-sm px-2.5 p-1.5">Touch</li>
                            <li class="border border-gray-500 rounded-2xl text-sm px-2.5 p-1.5">I Bachelors</li>
                            <li class="border border-gray-500 rounded-2xl text-sm px-2.5 p-1.5">Better in person</li>
                         </ul>
                    </div>

                    <div class="spacey-y-3 py-2">
                        <h3 class="font-bold text-xl py-2"> Lifestyle </h3>
                         <ul class="flex flex-wrap gap-3">
                            <li class="border border-gray-500 rounded-2xl text-sm px-2.5 p-1.5">Non Smoker</li>
                            <li class="border border-gray-500 rounded-2xl text-sm px-2.5 p-1.5">Gym</li>
                            <li class="border border-gray-500 rounded-2xl text-sm px-2.5 p-1.5">Travel</li>
                         </ul>
                    </div>

                 </section>




             </section>

             {{-- Actions --}}

             <section class="sticky bg-gradient-to-b from-white/50 to-white bottom-0 py-2 flex items-center justify-center gap-4 inset-x-0 mx-auto">

                            {{-- swipe left --}}
                            <div>
                                <button draggable="true" @click="swipeLeft()"
                                    class="bg-white rounded-full border-2 pointer-events-auto group border-red-600 p-2 shrink-0 max-w-fit flex items-center text-red-600  ">

                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                        stroke-width="3" stroke="currentColor"
                                        class="w-10 h-10 shrink-0 m-auto group-hover:scale-105 transition-transform 2">
                                        <path fill-rule="evenodd"
                                            d="M5.47 5.47a.75.75 0 0 1 1.06 0L12 10.94l5.47-5.47a.75.75 0 1 1 1.06 1.06L13.06 12l5.47 5.47a.75.75 0 1 1-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 0 1-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 0 1 0-1.06Z"
                                            clip-rule="evenodd" />
                                    </svg>

                                </button>

                            </div>

                            {{-- Super Like --}}
                            <div>
                                <button draggable="true" @click="swipeUp()"
                                    class="bg-white rounded-full border-2 pointer-events-auto group border-blue-600 p-1.5 shrink-0 max-w-fit flex items-center text-blue-600 scale-95 ">

                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                        class="w-11 h-11 shrink-0 m-auto group-hover:scale-105 transition-transform ">
                                        <path fill-rule="evenodd"
                                            d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z"
                                            clip-rule="evenodd" />
                                    </svg>


                                </button>

                            </div>

                            {{-- Swipe Right --}}
                            <div>
                                <button draggable="true" @click="swipeRight()"
                                    class="bg-white rounded-full border-2 pointer-events-auto group border-green-600 p-2 shrink-0 max-w-fit flex items-center text-green-600   ">


                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                        class="w-10 h-10 shrink-0 m-auto group-hover:scale-105 transition-transform ">
                                        <path
                                            d="m11.645 20.91-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.004-.003.001a.752.752 0 0 1-.704 0l-.003-.001Z" />
                                    </svg>


                                </button>

                            </div>

                
             </section>

            </div>


    </div>

    @endfor

</div>
</div>