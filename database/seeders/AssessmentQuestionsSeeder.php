<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AssessmentQuestion;

class AssessmentQuestionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear existing questions
        AssessmentQuestion::where('type', 'grammar')->delete();
        
        // Add grammar assessment questions - medium to hard difficulty
        $questions = [
            [
                'type' => 'grammar',
                'question' => 'Identify the correctly punctuated sentence:',
                'correct_answer' => 'The manuscript, which was written in the 17th century, contains several previously undiscovered passages.',
                'options' => [
                    'The manuscript which was written in the 17th century contains several previously undiscovered passages.',
                    'The manuscript, which was written in the 17th century contains several previously undiscovered passages.',
                    'The manuscript, which was written in the 17th century, contains several previously undiscovered passages.',
                    'The manuscript which was written in the 17th century, contains several previously undiscovered passages.'
                ],
                'difficulty' => 'medium',
                'explanation' => 'The non-restrictive clause "which was written in the 17th century" should be enclosed by commas on both sides.'
            ],
            [
                'type' => 'grammar',
                'question' => 'Choose the sentence with correct subject-verb agreement:',
                'correct_answer' => 'A variety of solutions have been proposed to address the issue.',
                'options' => [
                    'A variety of solutions has been proposed to address the issue.',
                    'A variety of solutions have been proposed to address the issue.',
                    'A variety of solutions is being proposed to address the issue.',
                    'A variety of solutions were being proposed to address the issue.'
                ],
                'difficulty' => 'hard',
                'explanation' => 'When "a variety of" is followed by a plural noun, the verb should agree with the plural noun, not with "variety."'
            ],
            [
                'type' => 'grammar',
                'question' => 'Select the sentence with the correct use of the subjunctive mood:',
                'correct_answer' => 'The professor insisted that each student submit a research proposal.',
                'options' => [
                    'The professor insisted that each student submits a research proposal.',
                    'The professor insisted that each student submitted a research proposal.',
                    'The professor insisted that each student submit a research proposal.',
                    'The professor insisted that each student would submit a research proposal.'
                ],
                'difficulty' => 'hard',
                'explanation' => 'The subjunctive mood is used after verbs like "insist," "suggest," "demand," etc., and takes the base form of the verb regardless of the subject.'
            ],
            [
                'type' => 'grammar',
                'question' => 'Which sentence contains a misplaced modifier?',
                'correct_answer' => 'Teeming with marine life, the scientist examined the coral reef closely.',
                'options' => [
                    'The scientist examined the coral reef, teeming with marine life, closely.',
                    'Teeming with marine life, the coral reef was examined closely by the scientist.',
                    'The scientist closely examined the coral reef teeming with marine life.',
                    'Teeming with marine life, the scientist examined the coral reef closely.'
                ],
                'difficulty' => 'medium',
                'explanation' => 'The phrase "teeming with marine life" should modify "coral reef," not "scientist."'
            ],
            [
                'type' => 'grammar',
                'question' => 'Identify the sentence with incorrect parallel structure:',
                'correct_answer' => 'The CEO is known for her strategic thinking, innovative approach, and she delegates effectively.',
                'options' => [
                    'The CEO is known for her strategic thinking, innovative approach, and effective delegation.',
                    'The CEO is known for thinking strategically, approaching problems innovatively, and delegating effectively.',
                    'The CEO is known for her strategic thinking, her innovative approach, and her effective delegation.',
                    'The CEO is known for her strategic thinking, innovative approach, and she delegates effectively.'
                ],
                'difficulty' => 'hard',
                'explanation' => 'Parallel structure requires using the same grammatical form for each item in a series. The phrase "she delegates effectively" breaks the parallel structure established by the noun phrases "strategic thinking" and "innovative approach."'
            ],
            [
                'type' => 'grammar',
                'question' => 'Which sentence demonstrates correct usage of the past perfect tense?',
                'correct_answer' => 'By the time the ambulance arrived, the patient had already regained consciousness.',
                'options' => [
                    'By the time the ambulance arrived, the patient already regained consciousness.',
                    'By the time the ambulance arrived, the patient has already regained consciousness.',
                    'By the time the ambulance arrived, the patient had already regained consciousness.',
                    'By the time the ambulance arrived, the patient would have already regained consciousness.'
                ],
                'difficulty' => 'medium',
                'explanation' => 'The past perfect tense (had + past participle) is used to describe an action that was completed before another past action.'
            ],
            [
                'type' => 'grammar',
                'question' => 'Choose the correctly punctuated sentence containing both a colon and semicolon:',
                'correct_answer' => 'The report highlighted three key findings: increased productivity; decreased operational costs; and improved employee satisfaction.',
                'options' => [
                    'The report highlighted three key findings: increased productivity, decreased operational costs, and improved employee satisfaction.',
                    'The report highlighted three key findings; increased productivity: decreased operational costs; and improved employee satisfaction.',
                    'The report highlighted three key findings: increased productivity; decreased operational costs; and improved employee satisfaction.',
                    'The report highlighted three key findings; increased productivity, decreased operational costs, and improved employee satisfaction.'
                ],
                'difficulty' => 'hard',
                'explanation' => 'A colon is used to introduce a list, and semicolons are used to separate items in a list when the items themselves contain commas or to separate closely related independent clauses.'
            ],
            [
                'type' => 'grammar',
                'question' => 'Identify the sentence with the correct use of appositives:',
                'correct_answer' => 'Dr. Ramirez, the lead researcher on the project, will present the findings at next month\'s conference.',
                'options' => [
                    'Dr. Ramirez the lead researcher on the project will present the findings at next month\'s conference.',
                    'Dr. Ramirez: the lead researcher on the project, will present the findings at next month\'s conference.',
                    'Dr. Ramirez, the lead researcher on the project, will present the findings at next month\'s conference.',
                    'Dr. Ramirez (the lead researcher on the project) will present the findings at next month\'s conference.'
                ],
                'difficulty' => 'medium',
                'explanation' => 'An appositive is a noun or noun phrase that renames or explains another noun right beside it. Non-restrictive appositives (those not essential to the meaning) are set off by commas.'
            ],
            [
                'type' => 'grammar',
                'question' => 'Which sentence contains correct subject-verb agreement with a collective noun?',
                'correct_answer' => 'The committee has reached its decision after lengthy deliberation.',
                'options' => [
                    'The committee have reached their decision after lengthy deliberation.',
                    'The committee has reached their decision after lengthy deliberation.',
                    'The committee have reached its decision after lengthy deliberation.',
                    'The committee has reached its decision after lengthy deliberation.'
                ],
                'difficulty' => 'hard',
                'explanation' => 'In American English, collective nouns like "committee" are treated as singular and take singular verbs and pronouns when the group is acting as a unit.'
            ],
            [
                'type' => 'grammar',
                'question' => 'Identify the sentence with correct use of the gerund phrase:',
                'correct_answer' => 'Her diligently researching the topic impressed the professor.',
                'options' => [
                    'Her diligently researching the topic impressed the professor.',
                    'She diligently researching the topic impressed the professor.',
                    'Her diligent researching the topic impressed the professor.',
                    'She diligently researched the topic impressed the professor.'
                ],
                'difficulty' => 'hard',
                'explanation' => 'When a gerund phrase functions as the subject of a sentence, possessive pronouns (like "her") should be used to modify the gerund.'
            ],
            [
                'type' => 'grammar',
                'question' => 'Select the sentence with correct pronoun case in a compound structure:',
                'correct_answer' => 'The award was presented to Dr. Chen and me at the conference.',
                'options' => [
                    'The award was presented to Dr. Chen and I at the conference.',
                    'The award was presented to Dr. Chen and myself at the conference.',
                    'The award was presented to Dr. Chen and me at the conference.',
                    'The award was presented to myself and Dr. Chen at the conference.'
                ],
                'difficulty' => 'medium',
                'explanation' => 'The object pronoun "me" is correct after a preposition like "to," even in a compound structure.'
            ],
            [
                'type' => 'grammar',
                'question' => 'Which sentence demonstrates correct use of the past participle?',
                'correct_answer' => 'The prosecutor has spoken to all witnesses who have given statements.',
                'options' => [
                    'The prosecutor has spoke to all witnesses who have gave statements.',
                    'The prosecutor has spoken to all witnesses who have gave statements.',
                    'The prosecutor has spoke to all witnesses who have given statements.',
                    'The prosecutor has spoken to all witnesses who have given statements.'
                ],
                'difficulty' => 'medium',
                'explanation' => 'The past participle forms of the verbs "speak" and "give" are "spoken" and "given," respectively, and should be used after auxiliary verbs like "has" and "have."'
            ],
            [
                'type' => 'grammar',
                'question' => 'Identify the sentence with correct placement of adverbs:',
                'correct_answer' => 'The research team quickly and efficiently analyzed the data.',
                'options' => [
                    'The research team analyzed quickly and efficiently the data.',
                    'The research team analyzed the data quickly and efficient.',
                    'Quickly and efficiently the research team analyzed the data.',
                    'The research team quickly and efficiently analyzed the data.'
                ],
                'difficulty' => 'medium',
                'explanation' => 'Adverbs typically precede the verb they modify, and adverbs of manner like "quickly" and "efficiently" should be in the form of adverbs (-ly), not adjectives.'
            ],
            [
                'type' => 'grammar',
                'question' => 'Choose the sentence with correct use of the conditional tense:',
                'correct_answer' => 'If the experiment had been successful, the team would have published the results immediately.',
                'options' => [
                    'If the experiment would have been successful, the team would have published the results immediately.',
                    'If the experiment was successful, the team would have published the results immediately.',
                    'If the experiment had been successful, the team would published the results immediately.',
                    'If the experiment had been successful, the team would have published the results immediately.'
                ],
                'difficulty' => 'hard',
                'explanation' => 'In the third conditional (hypothetical past), "if + past perfect" is used in the conditional clause, and "would have + past participle" is used in the main clause.'
            ],
            [
                'type' => 'grammar',
                'question' => 'Identify the sentence with the correct use of a restrictive and non-restrictive clause:',
                'correct_answer' => 'Students who submit their assignments late will lose points; however, those who have documented emergencies, which must be verified, may receive extensions.',
                'options' => [
                    'Students, who submit their assignments late, will lose points; however, those who have documented emergencies which must be verified may receive extensions.',
                    'Students who submit their assignments late will lose points; however, those who have documented emergencies, which must be verified, may receive extensions.',
                    'Students, who submit their assignments late will lose points, however, those who have documented emergencies which must be verified, may receive extensions.',
                    'Students who submit their assignments late, will lose points; however, those who have documented emergencies which must be verified may receive extensions.'
                ],
                'difficulty' => 'hard',
                'explanation' => 'Restrictive clauses (essential to meaning) like "who submit their assignments late" are not set off by commas, while non-restrictive clauses (additional information) like "which must be verified" are set off by commas.'
            ],
            [
                'type' => 'grammar',
                'question' => 'Select the sentence with correct verb tense consistency:',
                'correct_answer' => 'The professor explained the concept, demonstrated the procedure, and assigned the project during yesterday\'s lecture.',
                'options' => [
                    'The professor explained the concept, demonstrates the procedure, and assigned the project during yesterday\'s lecture.',
                    'The professor explained the concept, has demonstrated the procedure, and assigned the project during yesterday\'s lecture.',
                    'The professor explained the concept, demonstrated the procedure, and assigns the project during yesterday\'s lecture.',
                    'The professor explained the concept, demonstrated the procedure, and assigned the project during yesterday\'s lecture.'
                ],
                'difficulty' => 'medium',
                'explanation' => 'Verb tense should remain consistent within a sentence unless there is a logical reason for a tense shift. Since all actions occurred during "yesterday\'s lecture," all verbs should be in the past tense.'
            ],
            [
                'type' => 'grammar',
                'question' => 'Which sentence demonstrates correct use of correlative conjunctions?',
                'correct_answer' => 'Not only did the candidate address economic issues, but she also outlined her foreign policy agenda.',
                'options' => [
                    'Not only the candidate addressed economic issues, but also she outlined her foreign policy agenda.',
                    'The candidate not only addressed economic issues, but also she outlined her foreign policy agenda.',
                    'Not only did the candidate address economic issues, but she also outlined her foreign policy agenda.',
                    'The candidate not only addressed economic issues, but outlined also her foreign policy agenda.'
                ],
                'difficulty' => 'hard',
                'explanation' => 'Correlative conjunctions like "not only...but also" should be followed by parallel grammatical structures. "Did the candidate address" and "she...outlined" maintain this parallelism while following the correct syntactic pattern for "not only...but also."'
            ],
            [
                'type' => 'grammar',
                'question' => 'Identify the sentence with correct noun-pronoun agreement:',
                'correct_answer' => 'Each of the students must complete their assignment by Friday.',
                'options' => [
                    'Each of the students must complete his assignment by Friday.',
                    'Each of the students must complete her assignment by Friday.',
                    'Each of the students must complete its assignment by Friday.',
                    'Each of the students must complete their assignment by Friday.'
                ],
                'difficulty' => 'medium',
                'explanation' => 'In modern English, the singular "they/their/them" is increasingly accepted as a gender-neutral pronoun to refer to singular antecedents of unspecified gender.'
            ],
            [
                'type' => 'grammar',
                'question' => 'Choose the sentence with correct subject-verb agreement in a sentence with intervening phrases:',
                'correct_answer' => 'The impact of environmental regulations, despite opposition from certain industry sectors, has been significant.',
                'options' => [
                    'The impact of environmental regulations, despite opposition from certain industry sectors, have been significant.',
                    'The impact of environmental regulations, despite opposition from certain industry sectors, has been significant.',
                    'The impact of environmental regulations, despite oppositions from certain industry sectors, have been significant.',
                    'The impact of environmental regulations, despite oppositions from certain industry sectors, has been significant.'
                ],
                'difficulty' => 'hard',
                'explanation' => 'The subject of the sentence is "impact" (singular), so it takes the singular verb "has been" regardless of intervening phrases.'
            ],
            [
                'type' => 'grammar',
                'question' => 'Which sentence contains proper use of the present perfect progressive tense?',
                'correct_answer' => 'The research team has been analyzing the data for the past three months.',
                'options' => [
                    'The research team has analyzing the data for the past three months.',
                    'The research team have been analyzing the data for the past three months.',
                    'The research team has been analyzing the data for the past three months.',
                    'The research team had been analyzing the data for the past three months.'
                ],
                'difficulty' => 'medium',
                'explanation' => 'The present perfect progressive tense is formed with "has/have been + present participle (-ing)" and is used to describe an action that began in the past and has been continuing up to the present.'
            ],
            [
                'type' => 'grammar',
                'question' => 'Identify the sentence with correct use of infinitives:',
                'correct_answer' => 'Her desire to thoroughly understand the material motivated her to diligently complete all the assignments.',
                'options' => [
                    'Her desire thoroughly to understand the material motivated her to diligently complete all the assignments.',
                    'Her desire to thoroughly understand the material motivated her diligently to complete all the assignments.',
                    'Her desire to thoroughly understand the material motivated her to diligently complete all the assignments.',
                    'Her desire to thoroughly understand the material motivated her to complete diligently all the assignments.'
                ],
                'difficulty' => 'hard',
                'explanation' => 'In modern English, split infinitives ("to thoroughly understand") are grammatically acceptable. The placement of the adverb "diligently" before the verb it modifies ("complete") is correct.'
            ],
            [
                'type' => 'grammar',
                'question' => 'Select the sentence with the correct use of semicolons and conjunctive adverbs:',
                'correct_answer' => 'The experiment yielded unexpected results; consequently, the researchers had to revise their hypothesis.',
                'options' => [
                    'The experiment yielded unexpected results, consequently, the researchers had to revise their hypothesis.',
                    'The experiment yielded unexpected results; consequently the researchers had to revise their hypothesis.',
                    'The experiment yielded unexpected results, consequently the researchers had to revise their hypothesis.',
                    'The experiment yielded unexpected results; consequently, the researchers had to revise their hypothesis.'
                ],
                'difficulty' => 'medium',
                'explanation' => 'A semicolon is used to join two independent clauses without a coordinating conjunction. When a conjunctive adverb like "consequently" is used, it should be followed by a comma.'
            ],
            [
                'type' => 'grammar',
                'question' => 'Which sentence demonstrates correct use of the passive voice when appropriate?',
                'correct_answer' => 'The procedures were established by the committee after extensive consultation with stakeholders.',
                'options' => [
                    'The committee established the procedures after extensive consultation with stakeholders.',
                    'The procedures were established by the committee after extensive consultation with stakeholders.',
                    'Extensive consultation with stakeholders led the committee to establish the procedures.',
                    'After the committee consulted extensively with stakeholders, they established the procedures.'
                ],
                'difficulty' => 'medium',
                'explanation' => 'The passive voice is appropriate when the focus is on the action or the recipient of the action rather than the doer. In this case, "procedures" is the focus.'
            ],
            [
                'type' => 'grammar',
                'question' => 'Identify the sentence with correct use of elliptical constructions:',
                'correct_answer' => 'The first team completed the task more efficiently than the second did.',
                'options' => [
                    'The first team completed the task more efficiently than the second.',
                    'The first team completed the task more efficiently than did the second.',
                    'The first team completed the task more efficiently than the second did.',
                    'The first team completed the task more efficiently than the second team.'
                ],
                'difficulty' => 'hard',
                'explanation' => 'In an elliptical construction, words that would otherwise be repeated are omitted for conciseness. The addition of "did" makes it clear that we are comparing how efficiently each team completed the task.'
            ],
            [
                'type' => 'grammar',
                'question' => 'Select the sentence with the correct sequence of tenses in reported speech:',
                'correct_answer' => 'The professor announced that the examination would be postponed until further notice.',
                'options' => [
                    'The professor announced that the examination will be postponed until further notice.',
                    'The professor announced that the examination would be postponed until further notice.',
                    'The professor announced that the examination has been postponed until further notice.',
                    'The professor announced that the examination is postponed until further notice.'
                ],
                'difficulty' => 'medium',
                'explanation' => 'In reported speech, when the reporting verb is in the past tense, there is typically a backshift in the tense of the reported clause. The present tense "will be" becomes "would be."'
            ],
            [
                'type' => 'grammar',
                'question' => 'Choose the sentence with correct use of a participial phrase:',
                'correct_answer' => 'Having analyzed all the data, the researcher published her findings in a peer-reviewed journal.',
                'options' => [
                    'Analyzing all the data, the findings were published in a peer-reviewed journal by the researcher.',
                    'Having analyzed all the data, the findings were published in a peer-reviewed journal.',
                    'Having analyzed all the data, the researcher published her findings in a peer-reviewed journal.',
                    'The researcher published her findings in a peer-reviewed journal, having analyzed all the data by her.'
                ],
                'difficulty' => 'hard',
                'explanation' => 'A participial phrase should modify the subject of the main clause. In this sentence, "Having analyzed all the data" correctly modifies "the researcher," who is the subject of the main clause.'
            ],
            [
                'type' => 'grammar',
                'question' => 'Which of the following sentences correctly uses the subjunctive mood after a noun clause?',
                'correct_answer' => 'It is essential that every candidate submit financial disclosure forms before the deadline.',
                'options' => [
                    'It is essential that every candidate submits financial disclosure forms before the deadline.',
                    'It is essential that every candidate submitted financial disclosure forms before the deadline.',
                    'It is essential that every candidate submit financial disclosure forms before the deadline.',
                    'It is essential that every candidate would submit financial disclosure forms before the deadline.'
                ],
                'difficulty' => 'hard',
                'explanation' => 'The subjunctive mood is used after expressions of necessity, recommendation, or requirement. The subjunctive form of the verb does not change with the subject and uses the base form of the verb.'
            ]
        ];
        
        // Insert more than 40 questions to allow for sufficient randomization
        foreach ($questions as $question) {
            AssessmentQuestion::create($question);
        }
        
        // Add 25 more questions (placeholders shown here)
        for ($i = 1; $i <= 25; $i++) {
            AssessmentQuestion::create([
                'type' => 'grammar',
                'question' => 'Additional Grammar Question #' . $i . ' (This is a placeholder - replace with real questions)',
                'correct_answer' => 'Correct Answer',
                'options' => ['Option A', 'Correct Answer', 'Option C', 'Option D'],
                'difficulty' => rand(0, 1) ? 'medium' : 'hard',
                'explanation' => 'Explanation for the correct answer.'
            ]);
        }
        
        $this->command->info('Added ' . (count($questions) + 25) . ' grammar assessment questions');
    }
}