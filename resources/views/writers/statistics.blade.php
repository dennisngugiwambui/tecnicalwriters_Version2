@extends('writers.app')

@section('content')

<link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
<div class="flex-1 px-4 lg:px-8 pb-8 lg:ml-72 transition-all duration-300 pt-24">
    <main class="flex-1 max-w-7xl mx-auto bg-white shadow p-6 rounded-md">
        <h3 class="text-2xl font-semibold">My Dashboards</h3>
        
        <!-- Tabs -->
        <div class="mt-4 border-b flex space-x-4 text-lg">
            <button class="tab-button text-black font-semibold border-b-2 border-yellow-500 pb-2" data-tab="statistics">Statistics</button>
            <button class="tab-button text-gray-400 font-semibold pb-2" data-tab="rating-details">Rating Details</button>
            <button class="tab-button text-gray-400 font-semibold pb-2" data-tab="bonus-factors">Bonus Factors</button>
            <button class="tab-button text-gray-400 font-semibold pb-2" data-tab="company-values">Company Values</button>
            <button class="tab-button text-gray-400 font-semibold pb-2" data-tab="career-opportunities">Career Opportunities</button>
        </div>
        
        <!-- Rating Calculation Message -->
        <div class="bg-gray-100 p-4 rounded-md mt-4 text-gray-700">
            <p class="font-semibold">Your rating is calculated after each rating event (order approval, lateness fine, lateness fine removal, dispute resolution, and quality check) using the formula:</p>
            <div class="bg-white p-4 mt-2 rounded-md shadow">
                <p class="text-center font-semibold">Rating = (Quality Check points + Work Duration points + Disputes points + Requests points + Lateness points) / 100 * 100%</p>
            </div>
            <p class="mt-2">Please, locate the tables below to see the exact points distribution. You may also use our rating calculator (located below the tables with points) to see your potential rating.</p>
        </div>

        <!-- New Sections Before Quality Check -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6 mt-6">
            <div class="bg-white shadow-md p-4 rounded-md">
                <h5 class="text-md font-semibold">Lateness</h5>
                <table class="w-full border-collapse border border-gray-200 mt-2">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border border-gray-200 p-2 text-left">Count</th>
                            <th class="border border-gray-200 p-2 text-left">Points</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="border p-2">0</td><td class="border p-2">20</td></tr>
                        <tr><td class="border p-2">1</td><td class="border p-2">10</td></tr>
                        <tr><td class="border p-2">2+</td><td class="border p-2">0</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="bg-white shadow-md p-4 rounded-md">
                <h5 class="text-md font-semibold">Disputes</h5>
                <table class="w-full border-collapse border border-gray-200 mt-2">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border border-gray-200 p-2 text-left">Count</th>
                            <th class="border border-gray-200 p-2 text-left">Points</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="border p-2">0</td><td class="border p-2">20</td></tr>
                        <tr><td class="border p-2">1</td><td class="border p-2">10</td></tr>
                        <tr><td class="border p-2">2+</td><td class="border p-2">0</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="bg-white shadow-md p-4 rounded-md">
                <h5 class="text-md font-semibold">Work Duration</h5>
                <table class="w-full border-collapse border border-gray-200 mt-2">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border border-gray-200 p-2 text-left">Months</th>
                            <th class="border border-gray-200 p-2 text-left">Points</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="border p-2">0 - 4</td><td class="border p-2">0</td></tr>
                        <tr><td class="border p-2">5 - 8</td><td class="border p-2">0</td></tr>
                        <tr><td class="border p-2">9 - 12</td><td class="border p-2">0</td></tr>
                        <tr><td class="border p-2">13 - 17</td><td class="border p-2">0</td></tr>
                        <tr><td class="border p-2">18+</td><td class="border p-2">0</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="bg-white shadow-md p-4 rounded-md">
                <h5 class="text-md font-semibold">Customer Requests</h5>
                <table class="w-full border-collapse border border-gray-200 mt-2">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border border-gray-200 p-2 text-left">Count</th>
                            <th class="border border-gray-200 p-2 text-left">Points</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="border p-2">0</td><td class="border p-2">0</td></tr>
                        <tr><td class="border p-2">1</td><td class="border p-2">0</td></tr>
                        <tr><td class="border p-2">2</td><td class="border p-2">0</td></tr>
                        <tr><td class="border p-2">3+</td><td class="border p-2">0</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Quality Check Table -->
        <div class="mt-6 bg-white shadow-md p-4 rounded-md">
            <h5 class="text-md font-semibold">Quality Check</h5>
            <table class="w-full border-collapse border border-gray-200 mt-2">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-200 p-2 text-left">High School</th>
                        <th class="border border-gray-200 p-2 text-left">D</th>
                        <th class="border border-gray-200 p-2 text-left">C</th>
                        <th class="border border-gray-200 p-2 text-left">B</th>
                        <th class="border border-gray-200 p-2 text-left">A</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td class="border p-2">Undergrad. (yrs. 1-2)</td><td class="border p-2">10</td><td class="border p-2">10</td><td class="border p-2">10</td><td class="border p-2">10</td></tr>
                    <tr><td class="border p-2">Undergrad. (yrs. 3-4)</td><td class="border p-2">40</td><td class="border p-2">40</td><td class="border p-2">40</td><td class="border p-2">40</td></tr>
                    <tr><td class="border p-2">Graduate</td><td class="border p-2">60</td><td class="border p-2">60</td><td class="border p-2">60</td><td class="border p-2">60</td></tr>
                    <tr><td class="border p-2">PhD</td><td class="border p-2">59</td><td class="border p-2">59</td><td class="border p-2">59</td><td class="border p-2">59</td></tr>
                </tbody>
            </table>
        </div>
        
        <!-- Rating Section -->
        <div class="mt-6 bg-white shadow-md p-4 rounded-md">
            <div class="grid grid-cols-2 gap-4 bg-green-50 p-4 rounded-md">
                <div class="text-center">
                    <p class="text-gray-600 italic">Your Rating</p>
                    <p class="text-2xl font-bold">80.0%</p>
                </div>
                <div class="text-center">
                    <p class="text-gray-600 italic">Possible Rating</p>
                    <p class="text-2xl font-bold">80.0%</p>
                </div>
            </div>
            <table class="w-full border-collapse border border-gray-200 mt-2">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-200 p-2 text-left">Factors</th>
                        <th class="border border-gray-200 p-2 text-left">Your Rating</th>
                        <th class="border border-gray-200 p-2 text-left">Possible Rating</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td class="border p-2">Quality Check</td><td class="border p-2 font-semibold">Expert B</td><td class="border p-2">Expert B</td></tr>
                    <tr><td class="border p-2">Work Duration</td><td class="border p-2 font-semibold">18+ months</td><td class="border p-2">18+ months</td></tr>
                    <tr><td class="border p-2">Disputes</td><td class="border p-2 font-semibold">0</td><td class="border p-2">0</td></tr>
                    <tr><td class="border p-2">Lateness</td><td class="border p-2 font-semibold">0</td><td class="border p-2">0</td></tr>
                    <tr><td class="border p-2">Customer Requests</td><td class="border p-2 font-semibold">3+</td><td class="border p-2">3+</td></tr>
                </tbody>
            </table>
        </div>

        <!-- Bonus Factors -->
        <div id="bonus-factors" class="tab-content hidden mt-6">
            <h4 class="text-xl font-semibold mb-4">Bonus Factors</h4>
            <p class="text-gray-700">We take into account both quantitative and qualitative indicators of your work when evaluating performance.</p>
            <ul class="list-disc list-inside mt-2">
                <li>Completion of more than 10 orders in high season and 5 orders in low season.</li>
                <li>Customer ratings and request rate.</li>
                <li>Zero lateness and zero disputes for high and low season thresholds.</li>
            </ul>
            <p class="mt-2">The bonus may vary from 0% to 7.5% and is automatically added to your payment at the end of each month.</p>
        </div>
        
        <!-- Company Values -->
        <div id="company-values" class="tab-content hidden mt-6">
            <h4 class="text-xl font-semibold mb-4">Company Mission and Values</h4>
            <p class="text-gray-700">Our mission is to remain an ultimate quality and reliability standard in the professional writing market.</p>
            <h5 class="font-semibold mt-4">Company Goals</h5>
            <ul class="list-disc list-inside mt-2">
                <li>Development of high-quality products.</li>
                <li>Authentic, non-plagiarized papers.</li>
                <li>Timely delivery of customer orders.</li>
                <li>Strong, positive public image.</li>
            </ul>
            <h5 class="font-semibold mt-4">Core Values</h5>
            <ul class="list-disc list-inside mt-2">
                <li>Leadership</li>
                <li>Innovation</li>
                <li>Collaboration</li>
                <li>Unity</li>
                <li>Development</li>
                <li>Quality</li>
            </ul>
            <p class="mt-2">We believe in continuous improvement and strive to push our limits to achieve excellence.</p>
        </div>
    </main>
</div>

<script>
    $(document).ready(function() {
        $('.tab-button').click(function() {
            $('.tab-button').removeClass('text-black border-b-2 border-yellow-500');
            $(this).addClass('text-black border-b-2 border-yellow-500');
            $('.tab-content').hide();
            $('#' + $(this).data('tab')).show();
        });
    });
     document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll(".tab-button").forEach(button => {
            button.addEventListener("click", function () {
                document.querySelectorAll(".tab-button").forEach(btn => btn.classList.remove("text-black", "border-b-2", "border-yellow-500"));
                this.classList.add("text-black", "border-b-2", "border-yellow-500");
                document.querySelectorAll(".tab-content").forEach(content => content.classList.add("hidden"));
                document.getElementById(this.dataset.tab).classList.remove("hidden");
            });
        });
    });
</script>
@endsection
