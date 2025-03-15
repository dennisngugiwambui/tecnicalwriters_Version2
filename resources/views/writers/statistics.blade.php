@extends('writers.app')

@section('content')

<link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
<div class="flex-1 px-4 lg:px-8 pb-8 transition-all duration-300 pt-20 sm:pt-24 lg:ml-0 xl:ml-72">
    <main class="flex-1 max-w-7xl mx-auto bg-white shadow p-3 sm:p-6 rounded-md">
        <h3 class="text-xl sm:text-2xl font-semibold">My Dashboards</h3>
        
        <!-- Tabs -->
        <div class="mt-4 border-b overflow-x-auto pb-1 whitespace-nowrap">
            <div class="inline-flex space-x-2 sm:space-x-4 min-w-full px-1">
                <button class="tab-button text-black font-semibold border-b-2 border-yellow-500 pb-2 text-sm sm:text-base" data-tab="statistics">Statistics</button>
                <button class="tab-button text-gray-400 font-semibold pb-2 text-sm sm:text-base" data-tab="rating-details">Rating</button>
                <button class="tab-button text-gray-400 font-semibold pb-2 text-sm sm:text-base" data-tab="bonus-factors">Bonus</button>
                <button class="tab-button text-gray-400 font-semibold pb-2 text-sm sm:text-base" data-tab="company-values">Values</button>
                <button class="tab-button text-gray-400 font-semibold pb-2 text-sm sm:text-base" data-tab="career-opportunities">Career</button>
            </div>
        </div>
        
        <!-- Statistics Tab Content (Default) -->
        <div id="statistics" class="tab-content">
            <!-- Top Disciplines Section -->
            <div class="mt-4 sm:mt-6 bg-blue-100 p-3 sm:p-4 rounded-md">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-2 gap-3">
                    <h4 class="text-base sm:text-lg font-semibold">Top 10 disciplines</h4>
                    <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4 items-start sm:items-center w-full sm:w-auto">
                        <div class="grid grid-cols-2 gap-2 w-full sm:flex sm:w-auto">
                            <div class="flex items-center">
                                <span class="text-xs sm:text-sm mr-1 sm:mr-2 whitespace-nowrap">From</span>
                                <input type="date" id="top-disciplines-start-date" value="{{ date('Y-m-d', strtotime('-30 days')) }}" class="border rounded-md px-1 sm:px-2 py-1 text-xs sm:text-sm w-full">
                            </div>
                            <div class="flex items-center">
                                <span class="text-xs sm:text-sm mr-1 sm:mr-2 whitespace-nowrap">to</span>
                                <input type="date" id="top-disciplines-end-date" value="{{ date('Y-m-d') }}" class="border rounded-md px-1 sm:px-2 py-1 text-xs sm:text-sm w-full">
                            </div>
                        </div>
                        <button id="load-disciplines-btn" class="bg-blue-700 text-white px-3 sm:px-4 py-1 rounded-md text-xs sm:text-sm w-full sm:w-auto">Show stats</button>
                    </div>
                </div>
                
                <!-- Chart area -->
                <div class="bg-white p-3 sm:p-4 rounded-md">
                    <div id="disciplines-chart" class="w-full h-64">
                        <h5 class="text-center mb-4 text-sm sm:text-base">Orders</h5>
                        <div class="flex justify-between text-xs text-gray-500 mb-2 overflow-x-hidden">
                            <div>0.0</div>
                            <div>0.2</div>
                            <div>0.4</div>
                            <div>0.6</div>
                            <div>0.8</div>
                            <div>1.0</div>
                        </div>
                        
                        <div id="disciplines-bars" class="space-y-3 sm:space-y-4">
                            @foreach($topDisciplines as $discipline => $count)
                                @php
                                    $percentage = $totalOrders > 0 ? round(($count / $totalOrders) * 100) : 0;
                                    $width = $percentage . '%';
                                @endphp
                                <div class="flex items-center">
                                    <div class="bg-green-600 h-6 sm:h-8 rounded" style="width: {{ $width }}"></div>
                                    <span class="ml-2 text-xs sm:text-sm truncate">{{ $count }} ({{ $percentage }}%) {{ $discipline }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Finished orders and pages -->
            <div class="mt-4 sm:mt-6 bg-blue-100 p-3 sm:p-4 rounded-md">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-2 gap-3">
                    <h4 class="text-base sm:text-lg font-semibold">Finished orders and pages</h4>
                    <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4 items-start sm:items-center w-full sm:w-auto">
                        <div class="grid grid-cols-2 gap-2 w-full sm:flex sm:w-auto">
                            <div class="flex items-center">
                                <span class="text-xs sm:text-sm mr-1 sm:mr-2 whitespace-nowrap">From</span>
                                <input type="date" id="finished-orders-start-date" value="{{ date('Y-m-d', strtotime('-30 days')) }}" class="border rounded-md px-1 sm:px-2 py-1 text-xs sm:text-sm w-full">
                            </div>
                            <div class="flex items-center">
                                <span class="text-xs sm:text-sm mr-1 sm:mr-2 whitespace-nowrap">to</span>
                                <input type="date" id="finished-orders-end-date" value="{{ date('Y-m-d') }}" class="border rounded-md px-1 sm:px-2 py-1 text-xs sm:text-sm w-full">
                            </div>
                        </div>
                        <button id="load-orders-btn" class="bg-blue-700 text-white px-3 sm:px-4 py-1 rounded-md text-xs sm:text-sm w-full sm:w-auto">Show stats</button>
                    </div>
                </div>
                
                <!-- Chart area -->
                <div class="bg-white p-3 sm:p-4 rounded-md">
                    <div class="flex flex-wrap justify-center gap-3 sm:gap-6 mb-4">
                        <label class="flex items-center">
                            <input type="radio" name="chart-type" value="orders" checked class="mr-2">
                            <span class="text-xs sm:text-sm">Orders</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="chart-type" value="pages" class="mr-2">
                            <span class="text-xs sm:text-sm">Pages</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="chart-type" value="lateness" class="mr-2">
                            <span class="text-xs sm:text-sm">Lateness</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="chart-type" value="disputes" class="mr-2">
                            <span class="text-xs sm:text-sm">Disputes</span>
                        </label>
                    </div>
                    
                    <div class="grid grid-cols-4 justify-center text-sm font-medium text-center">
                        <div>{{ $totalCompletedOrders }}</div>
                        <div>{{ $totalPages }}</div>
                        <div>{{ $totalLateOrders }}</div>
                        <div>{{ $totalDisputes }}</div>
                    </div>
                    
                    <div class="grid grid-cols-4 justify-center text-xs text-gray-500 mb-4 text-center">
                        <div>Orders</div>
                        <div>{{ $latenessPercentage }}%</div>
                        <div>{{ $disputesPercentage }}%</div>
                        <div></div>
                    </div>
                    
                    <div id="monthly-chart" class="h-48 sm:h-64 mt-4">
                        <!-- Monthly chart will be rendered here -->
                    </div>
                </div>
            </div>
            
            <!-- Writer tenure info -->
            <div class="mt-4 sm:mt-6 bg-blue-100 p-3 sm:p-4 rounded-md">
                <div class="flex flex-wrap items-center justify-center text-center gap-2">
                    <div class="text-sm sm:text-lg">You have been</div>
                    <div class="bg-yellow-500 text-white font-bold rounded-md px-2 sm:px-3 py-1 text-sm sm:text-base">{{ $years }}</div>
                    <div class="text-sm sm:text-lg">years and</div>
                    <div class="bg-yellow-500 text-white font-bold rounded-md px-2 sm:px-3 py-1 text-sm sm:text-base">{{ $months }}</div>
                    <div class="text-sm sm:text-lg">months with UvoCorp.com</div>
                </div>
            </div>
            
            <!-- Writer info summary -->
            <div class="mt-4 bg-white p-3 sm:p-4 rounded-md">
                <div class="flex flex-col md:flex-row justify-between gap-2">
                    <div>
                        <p class="text-xs sm:text-sm text-gray-700">Registration date: <span class="font-medium">{{ $registrationDate }}</span></p>
                    </div>
                    <div>
                        <p class="text-xs sm:text-sm text-gray-700">Total Number of Orders Completed: <span class="font-medium">{{ $totalLifetimeOrders }}</span></p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Rating Details Tab Content -->
        <div id="rating-details" class="tab-content hidden">
            <!-- Rating Calculation Message -->
            <div class="bg-gray-100 p-3 sm:p-4 rounded-md mt-4 text-gray-700">
                <p class="font-semibold text-sm sm:text-base">Your rating is calculated after each rating event (order approval, lateness fine, lateness fine removal, dispute resolution, and quality check) using the formula:</p>
                <div class="bg-white p-3 sm:p-4 mt-2 rounded-md shadow">
                    <p class="text-center font-semibold text-xs sm:text-sm">Rating = (Quality Check points + Work Duration points + Disputes points + Requests points + Lateness points) / 100 * 100%</p>
                </div>
                <p class="mt-2 text-xs sm:text-sm">Please, locate the tables below to see the exact points distribution. You may also use our rating calculator (located below the tables with points) to see your potential rating.</p>
            </div>

            <!-- Rating Factors Tables -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mt-4 sm:mt-6">
                <div class="bg-white shadow-md p-3 sm:p-4 rounded-md">
                    <h5 class="text-sm sm:text-md font-semibold">Lateness</h5>
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse border border-gray-200 mt-2">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border border-gray-200 p-1 sm:p-2 text-left text-xs sm:text-sm">Count</th>
                                    <th class="border border-gray-200 p-1 sm:p-2 text-left text-xs sm:text-sm">Points</th>
                                </tr>
                            </thead>
                            <tbody class="text-xs sm:text-sm">
                                <tr><td class="border p-1 sm:p-2">0</td><td class="border p-1 sm:p-2">20</td></tr>
                                <tr><td class="border p-1 sm:p-2">1</td><td class="border p-1 sm:p-2">10</td></tr>
                                <tr><td class="border p-1 sm:p-2">2+</td><td class="border p-1 sm:p-2">0</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="bg-white shadow-md p-3 sm:p-4 rounded-md">
                    <h5 class="text-sm sm:text-md font-semibold">Disputes</h5>
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse border border-gray-200 mt-2">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border border-gray-200 p-1 sm:p-2 text-left text-xs sm:text-sm">Count</th>
                                    <th class="border border-gray-200 p-1 sm:p-2 text-left text-xs sm:text-sm">Points</th>
                                </tr>
                            </thead>
                            <tbody class="text-xs sm:text-sm">
                                <tr><td class="border p-1 sm:p-2">0</td><td class="border p-1 sm:p-2">20</td></tr>
                                <tr><td class="border p-1 sm:p-2">1</td><td class="border p-1 sm:p-2">10</td></tr>
                                <tr><td class="border p-1 sm:p-2">2+</td><td class="border p-1 sm:p-2">0</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="bg-white shadow-md p-3 sm:p-4 rounded-md">
                    <h5 class="text-sm sm:text-md font-semibold">Work Duration</h5>
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse border border-gray-200 mt-2">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border border-gray-200 p-1 sm:p-2 text-left text-xs sm:text-sm">Months</th>
                                    <th class="border border-gray-200 p-1 sm:p-2 text-left text-xs sm:text-sm">Points</th>
                                </tr>
                            </thead>
                            <tbody class="text-xs sm:text-sm">
                                <tr><td class="border p-1 sm:p-2">0 - 4</td><td class="border p-1 sm:p-2">0</td></tr>
                                <tr><td class="border p-1 sm:p-2">5 - 8</td><td class="border p-1 sm:p-2">5</td></tr>
                                <tr><td class="border p-1 sm:p-2">9 - 12</td><td class="border p-1 sm:p-2">10</td></tr>
                                <tr><td class="border p-1 sm:p-2">13 - 17</td><td class="border p-1 sm:p-2">15</td></tr>
                                <tr><td class="border p-1 sm:p-2">18+</td><td class="border p-1 sm:p-2">20</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="bg-white shadow-md p-3 sm:p-4 rounded-md">
                    <h5 class="text-sm sm:text-md font-semibold">Customer Requests</h5>
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse border border-gray-200 mt-2">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border border-gray-200 p-1 sm:p-2 text-left text-xs sm:text-sm">Count</th>
                                    <th class="border border-gray-200 p-1 sm:p-2 text-left text-xs sm:text-sm">Points</th>
                                </tr>
                            </thead>
                            <tbody class="text-xs sm:text-sm">
                                <tr><td class="border p-1 sm:p-2">0</td><td class="border p-1 sm:p-2">0</td></tr>
                                <tr><td class="border p-1 sm:p-2">1</td><td class="border p-1 sm:p-2">5</td></tr>
                                <tr><td class="border p-1 sm:p-2">2</td><td class="border p-1 sm:p-2">10</td></tr>
                                <tr><td class="border p-1 sm:p-2">3+</td><td class="border p-1 sm:p-2">20</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Quality Check Table -->
            <div class="mt-4 sm:mt-6 bg-white shadow-md p-3 sm:p-4 rounded-md">
                <h5 class="text-sm sm:text-md font-semibold">Quality Check</h5>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse border border-gray-200 mt-2">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border border-gray-200 p-1 sm:p-2 text-left text-xs sm:text-sm">High School</th>
                                <th class="border border-gray-200 p-1 sm:p-2 text-left text-xs sm:text-sm">D</th>
                                <th class="border border-gray-200 p-1 sm:p-2 text-left text-xs sm:text-sm">C</th>
                                <th class="border border-gray-200 p-1 sm:p-2 text-left text-xs sm:text-sm">B</th>
                                <th class="border border-gray-200 p-1 sm:p-2 text-left text-xs sm:text-sm">A</th>
                            </tr>
                        </thead>
                        <tbody class="text-xs sm:text-sm">
                            <tr><td class="border p-1 sm:p-2">Undergrad. (yrs. 1-2)</td><td class="border p-1 sm:p-2">10</td><td class="border p-1 sm:p-2">20</td><td class="border p-1 sm:p-2">30</td><td class="border p-1 sm:p-2">40</td></tr>
                            <tr><td class="border p-1 sm:p-2">Undergrad. (yrs. 3-4)</td><td class="border p-1 sm:p-2">40</td><td class="border p-1 sm:p-2">50</td><td class="border p-1 sm:p-2">60</td><td class="border p-1 sm:p-2">70</td></tr>
                            <tr><td class="border p-1 sm:p-2">Graduate</td><td class="border p-1 sm:p-2">60</td><td class="border p-1 sm:p-2">70</td><td class="border p-1 sm:p-2">80</td><td class="border p-1 sm:p-2">90</td></tr>
                            <tr><td class="border p-1 sm:p-2">PhD</td><td class="border p-1 sm:p-2">70</td><td class="border p-1 sm:p-2">80</td><td class="border p-1 sm:p-2">90</td><td class="border p-1 sm:p-2">100</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Rating Section -->
            <div class="mt-4 sm:mt-6 bg-white shadow-md p-3 sm:p-4 rounded-md">
                <div class="grid grid-cols-2 gap-2 sm:gap-4 bg-green-50 p-2 sm:p-4 rounded-md">
                    <div class="text-center">
                        <p class="text-gray-600 italic text-xs sm:text-sm">Your Rating</p>
                        <p class="text-xl sm:text-2xl font-bold">{{ $currentRating }}%</p>
                    </div>
                    <div class="text-center">
                        <p class="text-gray-600 italic text-xs sm:text-sm">Possible Rating</p>
                        <p class="text-xl sm:text-2xl font-bold">{{ $possibleRating }}%</p>
                    </div>
                </div>
                <div class="overflow-x-auto mt-2">
                    <table class="w-full border-collapse border border-gray-200">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border border-gray-200 p-1 sm:p-2 text-left text-xs sm:text-sm">Factors</th>
                                <th class="border border-gray-200 p-1 sm:p-2 text-left text-xs sm:text-sm">Your Rating</th>
                                <th class="border border-gray-200 p-1 sm:p-2 text-left text-xs sm:text-sm">Possible Rating</th>
                            </tr>
                        </thead>
                        <tbody class="text-xs sm:text-sm">
                            <tr><td class="border p-1 sm:p-2">Quality Check</td><td class="border p-1 sm:p-2 font-semibold">{{ $qualityLevel }}</td><td class="border p-1 sm:p-2">{{ $possibleQualityLevel }}</td></tr>
                            <tr><td class="border p-1 sm:p-2">Work Duration</td><td class="border p-1 sm:p-2 font-semibold">{{ $workDuration }} months</td><td class="border p-1 sm:p-2">{{ $workDuration }} months</td></tr>
                            <tr><td class="border p-1 sm:p-2">Disputes</td><td class="border p-1 sm:p-2 font-semibold">{{ $disputeCount }}</td><td class="border p-1 sm:p-2">{{ $possibleDisputeCount }}</td></tr>
                            <tr><td class="border p-1 sm:p-2">Lateness</td><td class="border p-1 sm:p-2 font-semibold">{{ $latenessCount }}</td><td class="border p-1 sm:p-2">{{ $possibleLatenessCount }}</td></tr>
                            <tr><td class="border p-1 sm:p-2">Customer Requests</td><td class="border p-1 sm:p-2 font-semibold">{{ $requestCount }}+</td><td class="border p-1 sm:p-2">{{ $possibleRequestCount }}+</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Bonus Factors Tab Content -->
        <div id="bonus-factors" class="tab-content hidden">
            <div class="mt-4 sm:mt-6 bg-white p-3 sm:p-6 rounded-md">
                <div class="mb-4 sm:mb-6 text-xs sm:text-sm">
                    <p>During the high-season period, there are many available orders on which you may freely choose to work. There are fewer orders in the low season months than during high season, and so we do not guarantee a fixed number of orders for each writer.</p>
                    <p class="mt-2 sm:mt-4">However, each freelance writer has a chance to take at least one order at a time so that there is equal opportunity for everyone to have some work.</p>
                </div>

                <h4 class="text-base sm:text-lg font-semibold mt-4 sm:mt-6">Company's Responsibilities</h4>
                <div class="border-t border-b border-gray-200 py-3 sm:py-4 my-3 sm:my-4 text-xs sm:text-sm">
                    <p>We would like to inform you about the bonus system that is applied during the evaluation of your monthly performance.</p>
                    <p class="mt-2 sm:mt-4">To begin with, we take into account both quantitative and qualitative indicators of your work. In particular:</p>
                    
                    <ul class="space-y-2 sm:space-y-3 mt-3 sm:mt-4">
                        <li class="flex items-start">
                            <span class="text-yellow-500 mr-2 flex-shrink-0">■</span>
                            <span>The amount of completed orders (all of them should be on finished status): more than 10 orders should be completed during a high season and more than 5 during a low season</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-yellow-500 mr-2 flex-shrink-0">■</span>
                            <span>Amount of pages written</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-yellow-500 mr-2 flex-shrink-0">■</span>
                            <span>Customers Mark</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-yellow-500 mr-2 flex-shrink-0">■</span>
                            <span>0% Lateness on condition that more than X* pages were written</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-yellow-500 mr-2 flex-shrink-0">■</span>
                            <span>0% Disputes on condition that more than X* pages were written</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-yellow-500 mr-2 flex-shrink-0">■</span>
                            <span>Request Rate (meaning the percentage of orders in which the clients requested you as the writer to all your finished orders).</span>
                        </li>
                    </ul>
                    
                    <p class="mt-3 sm:mt-4 italic">* X may vary and will be determined before the beginning of each low and high season.</p>
                </div>
                
                <div class="text-xs sm:text-sm">
                    <p>The bonus may vary from 0% to 7.5%, and is automatically added to your payment (for the completed orders) during the last night of the month.</p>
                    <p class="mt-2 sm:mt-4">You are welcome to check your monthly bonus in section "My Dashboards" → "Bonus Factors".</p>
                    <p class="mt-2 sm:mt-4">Please note that the bonuses do not apply to the writers who are currently in the Mentorship Program.</p>
                    <p class="mt-2 sm:mt-4">Besides, the Writers Department Administration may decide on seasonal bonuses for each writer at their own discretion.</p>
                    <p class="mt-2 sm:mt-4">Mind that your performance is the crucial factor that affects your writer's rating. The better your results, the more chances you have to be assigned a higher number of orders as well as more well-paid orders (Master's and Ph.D. levels).</p>
                    <p class="mt-2 sm:mt-4">We hope for a continued beneficial partnership with you, which is a key to the success of the company and your prosperity!</p>
                </div>
                
                <p class="mt-6 sm:mt-8 text-right text-xs sm:text-sm">Sincerely Yours,<br>Writers Department</p>
            </div>
        </div>
        
       <!-- Company Values Tab Content -->
<div id="company-values" class="tab-content hidden">
    <div class="mt-4 sm:mt-6 bg-white p-3 sm:p-6 rounded-md">
        <h4 class="text-lg sm:text-xl font-semibold mb-3 sm:mb-4">Company Mission and Values</h4>
        <p class="mb-4 sm:mb-6 text-xs sm:text-sm">Our mission is to remain an ultimate quality and reliability standard in the professional writing market.</p>
        
        <p class="mb-3 sm:mb-4 text-xs sm:text-sm">The company was established in 2006, when academic writing organizations had just begun to emerge. Since that time, we have grown from a small company into a sizeable enterprise, which understands the trends and developments that will shape our business in the future. Building a strong foundation, developing partnership relations, and keeping pace with the times are what make us the #1 workplace for writers from all over the world.</p>
        
        <h4 class="text-base sm:text-lg font-semibold mt-4 sm:mt-8 mb-3 sm:mb-4">Company Values and Goals</h4>
        <p class="mb-3 sm:mb-4 text-xs sm:text-sm">Our company's long-term goal is to become the performance quality standard in the market for custom academic writing. We strive for:</p>
        
        <ul class="space-y-2 sm:space-y-4 list-none mb-4 sm:mb-6 text-xs sm:text-sm">
            <li class="flex items-start">
                <span class="text-yellow-500 mr-2 flex-shrink-0">■</span>
                <span>Development of high quality products;</span>
            </li>
            <li class="flex items-start">
                <span class="text-yellow-500 mr-2 flex-shrink-0">■</span>
                <span>The composing of authentic, non-plagiarized papers;</span>
            </li>
            <li class="flex items-start">
                <span class="text-yellow-500 mr-2 flex-shrink-0">■</span>
                <span>Timely delivery of customer orders; and</span>
            </li>
            <li class="flex items-start">
                <span class="text-yellow-500 mr-2 flex-shrink-0">■</span>
                <span>The development of a strong, positive public image.</span>
            </li>
        </ul>
        
        <p class="mb-3 sm:mb-4 text-xs sm:text-sm">Our company appreciates its employees and sees our continued partnership through understanding and following the common values of the company. Our progress is demonstrated through:</p>
        
        <ul class="space-y-2 list-none mb-4 sm:mb-6 text-xs sm:text-sm">
            <li class="flex items-start">
                <span class="text-yellow-500 mr-2 flex-shrink-0">■</span>
                <span>Leadership;</span>
            </li>
            <li class="flex items-start">
                <span class="text-yellow-500 mr-2 flex-shrink-0">■</span>
                <span>Innovation;</span>
            </li>
            <li class="flex items-start">
                <span class="text-yellow-500 mr-2 flex-shrink-0">■</span>
                <span>Collaboration;</span>
            </li>
            <li class="flex items-start">
                <span class="text-yellow-500 mr-2 flex-shrink-0">■</span>
                <span>Unity;</span>
            </li>
            <li class="flex items-start">
                <span class="text-yellow-500 mr-2 flex-shrink-0">■</span>
                <span>Development; and</span>
            </li>
            <li class="flex items-start">
                <span class="text-yellow-500 mr-2 flex-shrink-0">■</span>
                <span>Quality.</span>
            </li>
        </ul>
        
        <p class="text-xs sm:text-sm">We are aware of the challenges but are never afraid of them, as they help us develop and move forward. For those in need, our Support Team is working 24/7. We also believe that sky is the limit, and the only way to push the limit is to reveal our potential, which is always greater than we think. Besides, we read regular writing guides and participate in quality improvement programs (Editing Project, Mentoring Program) since high quality is our priority. This way, we achieve our dreams and set new records.</p>
    </div>
</div>

<!-- Career Opportunities Tab Content -->
<div id="career-opportunities" class="tab-content hidden">
    <div class="mt-4 sm:mt-6 bg-white p-3 sm:p-6 rounded-md">
        <h4 class="text-lg sm:text-xl font-semibold mb-3 sm:mb-4">Career Development</h4>
        <p class="mb-4 sm:mb-6 text-xs sm:text-sm">By adhering to values of our company and showcasing constant drive for improvement, our writers get a chance of career development:</p>
        
        <ul class="space-y-4 sm:space-y-6 list-none text-xs sm:text-sm">
            <li class="flex items-start">
                <span class="text-yellow-500 mr-2 flex-shrink-0">■</span>
                <div>
                    <p>Upgrading the Academic Level: As long as you develop your skills, your account will be marked by our QC specialists as eligible for promotions, which will allow you to see more orders in general and have access to premium orders with a high price. More details may be found <a href="#" class="text-yellow-500">here</a>.</p>
                </div>
            </li>
            <li class="flex items-start">
                <span class="text-yellow-500 mr-2 flex-shrink-0">■</span>
                <div>
                    <p>Becoming a Thesis Writer: Experts with a high rating and commendable record of cooperation are invited (or may apply themselves) to write more complex assignments (30+ pages) on a regular basis. They receive assistance from managers in acquiring a steady flow of such orders.</p>
                </div>
            </li>
            <li class="flex items-start">
                <span class="text-yellow-500 mr-2 flex-shrink-0">■</span>
                <div>
                    <p>Becoming a STEM writer: If you prove yourself a natural expert in several disciplines from the following list:</p>
                    <p class="italic mt-2">Architecture, Building and Planning, Accounting, Finance, Statistics, Mathematics, Engineering, Civil Engineering, Computer Science, IT, Web, Biology, Chemistry, Physics, and Programming.</p>
                </div>
            </li>
        </ul>
        
        <p class="my-3 sm:my-6 text-xs sm:text-sm">We will invite you to participate in the STEM Program. You will receive orders from us in selected technical disciplines on a regular basis. Plus, you'll be working with a supervisor. Also, you will become a part of a STEM writers' community and get access to the exclusive team chat.</p>
        
        <p class="mb-3 sm:mb-6 text-xs sm:text-sm">Please note that the writers department invites potential candidates to join the STEM Program once/twice a year.</p>
        
        <ul class="space-y-4 sm:space-y-6 list-none text-xs sm:text-sm">
            <li class="flex items-start">
                <span class="text-yellow-500 mr-2 flex-shrink-0">■</span>
                <div>
                    <p>Becoming a Group Account: The company acknowledges accounts with 2 or more writers working in the same account to write more orders. Owners of such accounts can begin cooperation with Supervisor in Groups Team which will lead to:</p>
                    
                    <ul class="space-y-2 list-none mt-2 sm:mt-4 ml-4 sm:ml-6">
                        <li class="flex items-start">
                            <span class="text-yellow-500 mr-2 flex-shrink-0">■</span>
                            <span>Improvement of quality</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-yellow-500 mr-2 flex-shrink-0">■</span>
                            <span>Decrease in number of revisions</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-yellow-500 mr-2 flex-shrink-0">■</span>
                            <span>Bonuses</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-yellow-500 mr-2 flex-shrink-0">■</span>
                            <span>Possible promotion to Freelance Office</span>
                        </li>
                    </ul>
                </div>
            </li>
        </ul>
        
        <p class="mt-3 sm:mt-6 mb-2 sm:mb-4 text-xs sm:text-sm">To become a member of a Groups Team, the account needs to:</p>
        
        <ul class="space-y-2 list-none ml-4 sm:ml-6 text-xs sm:text-sm">
            <li class="flex items-start">
                <span class="text-yellow-500 mr-2 flex-shrink-0">■</span>
                <span>Be a group account with 2 or more writers per account</span>
            </li>
            <li class="flex items-start">
                <span class="text-yellow-500 mr-2 flex-shrink-0">■</span>
                <span>Have average monthly productivity of 20 or more orders</span>
            </li>
            <li class="flex items-start">
                <span class="text-yellow-500 mr-2 flex-shrink-0">■</span>
                <span>Have quality level of Undergrad. (yrs. 3-4) D and higher</span>
            </li>
        </ul>
        
        <p class="mt-3 sm:mt-6 text-xs sm:text-sm">If you match the following criteria and are ready to take the next step in your Uvoteam career, please contact Writers Dpt. via Messages.</p>
        
        <ul class="space-y-4 sm:space-y-6 mt-3 sm:mt-6 list-none text-xs sm:text-sm">
            <li class="flex items-start">
                <span class="text-yellow-500 mr-2 flex-shrink-0">■</span>
                <div>
                    <p>Becoming a Freelance Office: Only the best members of Groups Team, successful in providing a large number of high quality orders, can join the Freelance offices team. Under supervision of Offices Dpt. one gets the chance to progress further in terms of workload and CPP when providing perfect service. Candidates matching <a href="#" class="text-yellow-500">vacancy description</a> are hired before the beginning of each high season.</p>
                </div>
            </li>
            <li class="flex items-start">
                <span class="text-yellow-500 mr-2 flex-shrink-0">■</span>
                <div>
                    <p>Becoming a Mentor: The best performers get a chance to pass a test and be promoted to Mentors and guide the work of other writers.</p>
                </div>
            </li>
        </ul>
        
        <p class="mt-3 sm:mt-6 text-xs sm:text-sm">In general, every professional in our company has a chance to show their skills and willingness to improve their expertise and, as a result, receive a new career opportunity out of various options we offer. Unveil your potential, value quality, and overcome challenges to get those promotions!</p>
    </div>
</div>
</main>
</div>

<!-- Upload Modal with Multi-step Workflow - Responsive version -->
<div id="uploadModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center hidden z-50 upload-modal">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>

    <!-- Modal Container -->
    <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
        <!-- Modal Content -->
        <div id="uploadModalContent" class="relative bg-white rounded-lg w-full max-w-sm sm:max-w-md md:max-w-lg mx-auto shadow-xl transform transition-all">
            <!-- Step 1: File Selection -->
            <div id="uploadStep1" class="block">
                <!-- Modal Header -->
                <div class="flex justify-between items-center p-3 sm:p-6 border-b">
                    <h3 class="text-base sm:text-lg font-medium text-gray-900">Upload files</h3>
                    <button onclick="closeUploadModal()" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-3 sm:p-6">
                    <p class="text-xs sm:text-sm text-gray-600 mb-4 sm:mb-6">
                        Please make sure to upload a video preview together with the completed order. Select description "Preview" to your video file. Maximum file size allowed: 99 MB.
                    </p>

                    <!-- File List -->
                    <div id="uploadedFiles" class="space-y-2 sm:space-y-3 mb-3 sm:mb-4 max-h-40 overflow-y-auto"></div>

                    <!-- Upload Zone -->
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 sm:p-8 text-center cursor-pointer hover:border-green-500 transition-colors duration-200"
                         id="dropZone"
                         ondrop="handleFileDrop(event)"
                         ondragover="handleDragOver(event)"
                         ondragleave="handleDragLeave(event)"
                         onclick="document.getElementById('fileInput').click()">
                        <input type="file" id="fileInput" class="hidden" multiple onchange="handleFileSelect(event)">
                        <button type="button" class="text-green-500 font-medium text-xs sm:text-sm">Choose file</button>
                        <span class="text-gray-500 ml-2 text-xs sm:text-sm">or drag file</span>
                    </div>

                    <!-- Modal Footer -->
                    <div class="mt-4 sm:mt-6 flex justify-end space-x-3">
                        <button onclick="closeUploadModal()" 
                                class="px-3 sm:px-4 py-1 sm:py-2 text-gray-700 hover:text-gray-900 transition-colors duration-200 rounded-md text-xs sm:text-sm">
                            Cancel
                        </button>
                        <button onclick="gotoVerificationStep()" 
                                class="px-3 sm:px-4 py-1 sm:py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors duration-200 text-xs sm:text-sm">
                            Continue
                        </button>
                    </div>
                </div>
            </div>

            <!-- Step 2: Verification Checklist -->
            <div id="uploadStep2" class="hidden">
                <!-- Modal Header -->
                <div class="flex justify-between items-center p-3 sm:p-6 border-b">
                    <h3 class="text-base sm:text-lg font-medium text-gray-900">Upload files</h3>
                    <button onclick="closeUploadModal()" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-3 sm:p-6">
                    <h4 class="text-sm sm:text-base font-medium text-gray-700 mb-2 sm:mb-3">Paper details</h4>
                    
                    <div class="space-y-2 sm:space-y-4 text-xs sm:text-sm">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Paper format</span>
                            <span class="text-gray-800">Not applicable</span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Pages</span>
                            <div>
                                <span class="text-gray-800">0 pages</span>
                                <span class="text-gray-500 text-xs ml-1">(~0 words)</span>
                                <div class="text-xs text-gray-500">Double spaced</div>
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Sources to be cited</span>
                            <span class="text-gray-800">0</span>
                        </div>
                    </div>
                    
                    <div class="mt-4 sm:mt-6 text-xs sm:text-sm">
                        <h4 class="text-sm sm:text-base font-medium text-gray-700 mb-2 sm:mb-3">To be on the safe side, please, double-check whether:</h4>
                        <div class="space-y-2 sm:space-y-3">
                            <label class="flex items-start">
                                <input type="checkbox" class="form-checkbox h-4 w-4 sm:h-5 sm:w-5 text-green-500 rounded border-gray-300 mt-0.5">
                                <span class="ml-2 text-gray-700">All order files are checked</span>
                            </label>
                            <label class="flex items-start">
                                <input type="checkbox" class="form-checkbox h-4 w-4 sm:h-5 sm:w-5 text-green-500 rounded border-gray-300 mt-0.5">
                                <span class="ml-2 text-gray-700">All order messages are thoroughly read</span>
                            </label>
                            <label class="flex items-start">
                                <input type="checkbox" class="form-checkbox h-4 w-4 sm:h-5 sm:w-5 text-green-500 rounded border-gray-300 mt-0.5">
                                <span class="ml-2 text-gray-700">All paper instructions are followed</span>
                            </label>
                            <label class="flex items-start">
                                <input type="checkbox" class="form-checkbox h-4 w-4 sm:h-5 sm:w-5 text-green-500 rounded border-gray-300 mt-0.5">
                                <span class="ml-2 text-gray-700">Number of sources is as requested</span>
                            </label>
                            <label class="flex items-start">
                                <input type="checkbox" class="form-checkbox h-4 w-4 sm:h-5 sm:w-5 text-green-500 rounded border-gray-300 mt-0.5">
                                <span class="ml-2 text-gray-700">Required formatting style is applied</span>
                            </label>
                        </div>
                        
                        <div class="mt-3 sm:mt-4 p-2 sm:p-3 bg-gray-50 text-xs sm:text-sm text-gray-600 rounded-lg">
                            Plagiarism report will be available within 5-10 minutes in the Files section.
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="mt-4 sm:mt-6 flex justify-end space-x-3">
                        <button onclick="cancelVerification()" 
                                class="px-3 sm:px-4 py-1 sm:py-2 text-gray-700 hover:text-gray-900 transition-colors duration-200 rounded-md text-xs sm:text-sm">
                            Cancel
                        </button>
                        <button onclick="startUpload()" 
                                class="px-3 sm:px-4 py-1 sm:py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors duration-200 text-xs sm:text-sm">
                            Submit
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Processing Modal (Step 3) -->
<div id="processingModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50">
    <div class="bg-white rounded-lg p-4 sm:p-8 max-w-xs sm:max-w-md w-full mx-4">
        <h3 class="text-base sm:text-lg font-medium text-gray-700 text-center mb-4 sm:mb-6">Processing...</h3>
        <div class="w-full bg-gray-200 rounded-full h-2 sm:h-2.5 mb-4">
            <div id="uploadProgressBar" class="bg-green-500 h-2 sm:h-2.5 rounded-full" style="width: 0%"></div>
        </div>
    </div>
</div>

<!-- Success Modal (Step 4) -->
<div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50">
    <div class="bg-white rounded-lg p-4 sm:p-8 max-w-xs sm:max-w-md w-full mx-4 text-center">
        <div class="w-12 h-12 sm:w-16 sm:h-16 mx-auto mb-3 sm:mb-4 bg-green-50 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6 sm:w-8 sm:h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <h3 class="text-lg sm:text-xl font-medium text-gray-700">Success</h3>
    </div>
</div>

<!-- Toaster Notification -->
<div id="toaster" class="fixed top-4 right-4 z-50 transform translate-x-full transition-transform duration-300 ease-in-out">
    <div class="bg-green-50 border-l-4 border-green-500 p-3 sm:p-4 rounded shadow-lg flex items-start max-w-xs sm:max-w-sm">
        <div class="text-green-500 mr-2 sm:mr-3 flex-shrink-0">
            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <div>
            <p class="font-medium text-green-800 text-sm sm:text-base">Success!</p>
            <p class="text-xs sm:text-sm text-green-700 mt-1">Your files have been uploaded successfully.</p>
        </div>
        <button onclick="hideToaster()" class="ml-auto text-green-500 hover:text-green-700 flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Tab switching - simplified and more direct approach
    $('.tab-button').on('click', function() {
        const tabId = $(this).data('tab');
        
        // Update tab buttons styling
        $('.tab-button').removeClass('text-black border-b-2 border-yellow-500').addClass('text-gray-400');
        $(this).addClass('text-black border-b-2 border-yellow-500').removeClass('text-gray-400');
        
        // Hide all tab contents first
        $('.tab-content').hide();
        
        // Show the selected tab content
        $('#' + tabId).show();
    });
    
    // Ensure the first tab (statistics) is shown by default
    $('#statistics').show();
    
    // Load discipline stats
    $('#load-disciplines-btn').on('click', function() {
        const startDate = $('#top-disciplines-start-date').val();
        const endDate = $('#top-disciplines-end-date').val();
        
        if (!startDate || !endDate) {
            alert('Please select date range');
            return;
        }
        
        loadDisciplinesData(startDate, endDate);
    });
    
    // Load orders stats
    $('#load-orders-btn').on('click', function() {
        const startDate = $('#finished-orders-start-date').val();
        const endDate = $('#finished-orders-end-date').val();
        
        if (!startDate || !endDate) {
            alert('Please select date range');
            return;
        }
        
        loadOrdersData(startDate, endDate);
    });
    
    // Toggle chart type
    $('input[name="chart-type"]').on('change', function() {
        const chartType = $(this).val();
        const startDate = $('#finished-orders-start-date').val();
        const endDate = $('#finished-orders-end-date').val();
        
        if (startDate && endDate) {
            loadOrdersData(startDate, endDate, chartType);
        }
    });
    
    // Function to load disciplines data
    function loadDisciplinesData(startDate, endDate) {
        // Show loading state
        $('#disciplines-bars').html('<div class="text-center py-6 sm:py-8">Loading data...</div>');
        
        // Fetch data from backend
        $.ajax({
            url: '/statistics/disciplines',
            method: 'GET',
            data: {
                start_date: startDate,
                end_date: endDate
            },
            success: function(response) {
                if (response.success) {
                    renderDisciplinesChart(response.data);
                } else {
                    $('#disciplines-bars').html('<div class="text-center py-6 sm:py-8 text-red-500">Error loading data</div>');
                }
            },
            error: function() {
                $('#disciplines-bars').html('<div class="text-center py-6 sm:py-8 text-red-500">Error loading data</div>');
            }
        });
    }
    
    // Function to render disciplines chart
    function renderDisciplinesChart(data) {
        if (!data || !data.disciplines || data.disciplines.length === 0) {
            $('#disciplines-bars').html('<div class="text-center py-6 sm:py-8">No data available for the selected period</div>');
            return;
        }
        
        const totalOrders = data.total_orders;
        let barsHtml = '';
        
        data.disciplines.forEach(function(item) {
            const percentage = totalOrders > 0 ? Math.round((item.count / totalOrders) * 100) : 0;
            const width = percentage + '%';
            
            barsHtml += `
            <div class="flex items-center">
                <div class="bg-green-600 h-6 sm:h-8 rounded" style="width: ${width}"></div>
                <span class="ml-2 text-xs sm:text-sm truncate">${item.count} (${percentage}%) ${item.discipline}</span>
            </div>`;
        });
        
        $('#disciplines-bars').html(barsHtml);
    }
    
    // Function to load orders data
    function loadOrdersData(startDate, endDate, chartType = 'orders') {
        // Show loading state
        $('#monthly-chart').html('<div class="text-center py-6 sm:py-8">Loading data...</div>');
        
        // Fetch data from backend
        $.ajax({
            url: '/statistics/orders',
            method: 'GET',
            data: {
                start_date: startDate,
                end_date: endDate,
                chart_type: chartType
            },
            success: function(response) {
                if (response.success) {
                    renderOrdersChart(response.data, chartType);
                } else {
                    $('#monthly-chart').html('<div class="text-center py-6 sm:py-8 text-red-500">Error loading data</div>');
                }
            },
            error: function() {
                $('#monthly-chart').html('<div class="text-center py-6 sm:py-8 text-red-500">Error loading data</div>');
            }
        });
    }
    
    // Function to render orders chart
    function renderOrdersChart(data, chartType) {
        if (!data || !data.months || data.months.length === 0) {
            $('#monthly-chart').html('<div class="text-center py-6 sm:py-8">No data available for the selected period</div>');
            return;
        }
        
        // Clear previous chart
        $('#monthly-chart').html('<canvas id="orders-canvas" height="300"></canvas>');
        
        const labels = data.months.map(item => item.month);
        let values = [];
        let chartTitle = '';
        
        switch(chartType) {
            case 'pages':
                values = data.months.map(item => item.pages);
                chartTitle = 'Pages per Month';
                break;
            case 'lateness':
                values = data.months.map(item => item.late_orders);
                chartTitle = 'Late Orders per Month';
                break;
            case 'disputes':
                values = data.months.map(item => item.disputes);
                chartTitle = 'Disputes per Month';
                break;
            default: // orders
                values = data.months.map(item => item.orders);
                chartTitle = 'Orders per Month';
                break;
        }
        
        // Create chart with responsive options
        const ctx = document.getElementById('orders-canvas').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: chartTitle,
                    data: values,
                    backgroundColor: 'rgba(139, 195, 74, 0.6)',
                    borderColor: 'rgba(139, 195, 74, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            font: {
                                size: window.innerWidth < 768 ? 10 : 12
                            }
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                size: window.innerWidth < 768 ? 10 : 12
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            font: {
                                size: window.innerWidth < 768 ? 11 : 14
                            }
                        }
                    },
                    tooltip: {
                        bodyFont: {
                            size: window.innerWidth < 768 ? 11 : 14
                        },
                        titleFont: {
                            size: window.innerWidth < 768 ? 12 : 16
                        }
                    }
                }
            }
        });
    }
    
    // Load initial data
    const today = new Date();
    const oneMonthAgo = new Date();
    oneMonthAgo.setMonth(today.getMonth() - 1);
    
    // Format dates to YYYY-MM-DD
    const formatDate = date => {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    };
    
    const defaultStartDate = formatDate(oneMonthAgo);
    const defaultEndDate = formatDate(today);
    
    // Set default dates
    $('#top-disciplines-start-date').val(defaultStartDate);
    $('#top-disciplines-end-date').val(defaultEndDate);
    $('#finished-orders-start-date').val(defaultStartDate);
    $('#finished-orders-end-date').val(defaultEndDate);
    
    // Load initial data
    loadDisciplinesData(defaultStartDate, defaultEndDate);
    loadOrdersData(defaultStartDate, defaultEndDate, 'orders');
    
    // Handle window resize for responsive charts
    $(window).on('resize', function() {
        const startDate = $('#finished-orders-start-date').val();
        const endDate = $('#finished-orders-end-date').val();
        const chartType = $('input[name="chart-type"]:checked').val();
        
        if (startDate && endDate) {
            // Redraw charts when window size changes for better responsiveness
            loadOrdersData(startDate, endDate, chartType);
            loadDisciplinesData(startDate, endDate);
        }
    });
});

// File upload handlers - keeping these outside the jQuery ready function
// to make them globally accessible for the HTML onclick attributes
function handleFileDrop(event) {
    event.preventDefault();
    
    const dt = event.dataTransfer;
    const files = dt.files;
    
    handleFiles(files);
    document.getElementById('dropZone').classList.remove('border-green-500');
    document.getElementById('dropZone').classList.add('border-gray-300');
}

function handleDragOver(event) {
    event.preventDefault();
    document.getElementById('dropZone').classList.remove('border-gray-300');
    document.getElementById('dropZone').classList.add('border-green-500');
}

function handleDragLeave(event) {
    event.preventDefault();
    document.getElementById('dropZone').classList.remove('border-green-500');
    document.getElementById('dropZone').classList.add('border-gray-300');
}

function handleFileSelect(event) {
    const files = event.target.files;
    handleFiles(files);
}

function handleFiles(files) {
    const fileListContainer = document.getElementById('uploadedFiles');
    
    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        const fileItem = createFileItem(file);
        fileListContainer.appendChild(fileItem);
    }
}

function createFileItem(file) {
    const fileItem = document.createElement('div');
    fileItem.className = 'flex justify-between items-center bg-gray-50 p-2 sm:p-3 rounded-md';
    
    const fileSize = formatFileSize(file.size);
    const fileName = file.name.length > 20 ? file.name.substring(0, 17) + '...' : file.name;
    
    fileItem.innerHTML = `
        <div class="flex items-center">
            <div class="text-gray-500 mr-2">
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <div>
                <div class="text-xs sm:text-sm text-gray-800" title="${file.name}">${fileName}</div>
                <div class="text-xs text-gray-500">${fileSize}</div>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <select class="text-xs sm:text-sm border border-gray-300 rounded-md p-1">
                <option>Original</option>
                <option>Preview</option>
                <option>Additional</option>
            </select>
            <button onclick="removeFile(this)" class="text-gray-500 hover:text-red-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `;
    
    return fileItem;
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function removeFile(button) {
    const fileItem = button.closest('div.flex.justify-between');
    fileItem.remove();
}

function closeUploadModal() {
    document.getElementById('uploadModal').classList.add('hidden');
    resetUploadModal();
}

function gotoVerificationStep() {
    const fileListContainer = document.getElementById('uploadedFiles');
    if (fileListContainer.children.length === 0) {
        alert('Please upload at least one file');
        return;
    }
    
    document.getElementById('uploadStep1').classList.add('hidden');
    document.getElementById('uploadStep2').classList.remove('hidden');
}

function cancelVerification() {
    document.getElementById('uploadStep2').classList.add('hidden');
    document.getElementById('uploadStep1').classList.remove('hidden');
}

function startUpload() {
    // Validate checkboxes
    const checkboxes = document.querySelectorAll('#uploadStep2 input[type="checkbox"]');
    let allChecked = true;
    
    checkboxes.forEach(checkbox => {
        if (!checkbox.checked) {
            allChecked = false;
        }
    });
    
    if (!allChecked) {
        alert('Please check all items in the verification list');
        return;
    }
    
    // Hide verification step
    document.getElementById('uploadModal').classList.add('hidden');
    
    // Show processing modal
    document.getElementById('processingModal').classList.remove('hidden');
    
    // Simulate upload progress
    let progress = 0;
    const progressBar = document.getElementById('uploadProgressBar');
    
    const progressInterval = setInterval(() => {
        progress += 5;
        progressBar.style.width = progress + '%';
        
        if (progress >= 100) {
            clearInterval(progressInterval);
            
            // Hide processing modal
            document.getElementById('processingModal').classList.add('hidden');
            
            // Show success modal
            document.getElementById('successModal').classList.remove('hidden');
            
            // Auto hide success modal after 2 seconds
            setTimeout(() => {
                document.getElementById('successModal').classList.add('hidden');
                showToaster();
                resetUploadModal();
            }, 2000);
        }
    }, 100);
}

function resetUploadModal() {
    // Clear file list
    document.getElementById('uploadedFiles').innerHTML = '';
    
    // Reset file input
    document.getElementById('fileInput').value = '';
    
    // Reset checkboxes
    const checkboxes = document.querySelectorAll('#uploadStep2 input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    
    // Show step 1
    document.getElementById('uploadStep1').classList.remove('hidden');
    document.getElementById('uploadStep2').classList.add('hidden');
    
    // Reset progress bar
    document.getElementById('uploadProgressBar').style.width = '0%';
}

function showToaster() {
    const toaster = document.getElementById('toaster');
    toaster.classList.remove('translate-x-full');
    
    // Auto hide toaster after 5 seconds
    setTimeout(hideToaster, 5000);
}

function hideToaster() {
    const toaster = document.getElementById('toaster');
    toaster.classList.add('translate-x-full');
}

// Handle mobile menu toggle
function toggleMobileMenu() {
    const sidebar = document.getElementById('sidebar');
    if (sidebar) {
        sidebar.classList.toggle('-translate-x-full');
        
        const overlay = document.getElementById('sidebar-overlay');
        if (overlay) {
            overlay.classList.toggle('hidden');
        }
    }
}

// Ensure proper sidebar behavior on window resize
window.addEventListener('resize', function() {
    if (window.innerWidth >= 1024) {
        const sidebar = document.getElementById('sidebar');
        if (sidebar) {
            sidebar.classList.remove('-translate-x-full');
            
            const overlay = document.getElementById('sidebar-overlay');
            if (overlay) {
                overlay.classList.add('hidden');
            }
        }
    }
});

// Initialize any remaining UI elements that might need it
document.addEventListener('DOMContentLoaded', function() {
    // Make sure the toaster is initially hidden
    const toaster = document.getElementById('toaster');
    if (toaster) {
        toaster.classList.add('translate-x-full');
    }
});
</script>
@endsection