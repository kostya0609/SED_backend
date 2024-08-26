<?php
namespace SED\Documents\Directive\Seeders\Test;

use Illuminate\Support\Collection;
use SED\Documents\Directive\Dto\CreateUpdateDirectiveDto;
use SED\Documents\Directive\Seeders\SeederInterface;
use SED\Documents\Directive\Services\DirectiveService;

class DirectiveTestSeeder implements SeederInterface
{
	public function run()
	{
		ini_set('memory_limit', '-1');
		
		foreach (range(1, 3) as $_) {
			try {
				$directive = $this->generateDocument();
				echo "{$directive->number}\n";
			} catch (\Throwable $e) {
				\Log::error($e->getMessage(), [$e]);
			}
		}
	}

	private DirectiveService $service;
	public function __construct(DirectiveService $service)
	{
		$this->service = $service;
	}

	private function getUsers(): Collection
	{
		return collect([
			15296,
			15221,
			14936,
			14750,
			14328,
			14333,
			14115,
			14067,
			14073,
			13329,
			13318,
			13220,
			11802,
			9455,
			5587,
			10860,
			14991,
			14650,
			14661,
			14660,
			14675,
			14674,
			14673,
			14676,
			15280,
			14685,
			14692,
			14704,
			14724,
			14735,
			14765,
			14718,
			14719,
			14720,
			14729,
			14734,
			14743,
			14737,
			14749,
			14752,
			14758,
			14764,
			14770,
			14772,
			14780,
			14781,
			14788,
			14793,
			14820,
			14819,
			14823,
			14833,
			14839,
			14837,
			14836,
			14844,
			14867,
			14869,
			14875,
			14890,
			14895,
			14896,
			14898,
			14904,
			14908,
			14919,
			14915,
			14923,
			14920,
			14926,
			14999,
			14931,
			14948,
			14952,
			14956,
			14958,
			14968,
			14965,
			14966,
			14973,
			14970,
			14974,
			14980,
			14975,
			14981,
			14989,
			14988,
			14994,
			15001,
			15008,
			15030,
			15025,
			15032,
			15036,
			15044,
			15056,
			15062,
			15057,
			15060,
			15061,
			15065,
			15066,
			15067,
			15070,
			15075,
			15084,
			15082,
			15081,
			15085,
			15091,
			15087,
			15098,
			15093,
			15094,
			15090,
			15102,
			15097,
			15104,
			15105,
			15103,
			15111,
			15114,
			15120,
			15118,
			15123,
			15124,
			15132,
			15129,
			15135,
			15144,
			15137,
			15140,
			15142,
			15151,
			15148,
			15150,
			15153,
			15161,
			15162,
			15163,
			15167,
			15168,
			15172,
			15170,
			15178,
			15176,
			15180,
			15183,
			15184,
			15185,
			15186,
			15189,
			15193,
			15198,
			15201,
			15196,
			15204,
			15203,
			15216,
			15212,
			15222,
			15224,
			15217,
			15226,
			15231,
			15228,
			15232,
			15235,
			15238,
			15234,
			15243,
			15239,
			15242,
			15252,
			15247,
			15245,
			15253,
			15258,
			15259,
			15260,
			15267,
			15262,
			15266,
			15263,
			15268,
			15265,
			15271,
			15274,
			15277,
			15272,
			15273,
			15282,
			15281,
			15279,
			15278,
			15275,
			15287,
			15285,
			15283,
			15295,
			15288,
			15289,
			15292,
			15290,
			14811,
			15294,
			15293,
			14679,
			14697,
			14979,
			14777,
			14714,
			14708,
			12689,
			14950,
			12764,
			12790,
			12810,
			12849,
			12906,
			12926,
			12954,
			12982,
			13005,
			13047,
			12658,
			12654,
			14283,
			14289,
			14292,
			14301,
			14311,
			14310,
			14316,
			14317,
			14320,
			14324,
			14343,
			5406,
			14375,
			9553,
			9609,
			9598,
			13260,
			13292,
			13307,
			13309,
			13322,
			13345,
			13370,
			15254,
			14969,
			13429,
			13422,
			13423,
			13424,
			13432,
			5435,
			11737,
			11711,
			11640,
			14480,
			14491,
			14494,
			14611,
			14608,
			14617,
			14627,
			14636,
			14637,
			14643,
			5482,
			5496,
			5513,
			13529,
			13523,
			13539,
			13549,
			5526,
			14090,
			5530,
			14098,
			14110,
			12316,
			14087,
			5533,
			12678,
			13111,
			11932,
			5559,
			5567,
			12441,
			12468,
			10643,
			12595,
			12613,
			5662,
			11579,
			12029,
			9861,
			11644,
			15076,
			13080,
			6362,
			14177,
			14196,
			15024,
			14213,
			13571,
			13577,
			13598,
			13609,
			5725,
			14218,
			14220,
			14223,
			14229,
			14227,
			14233,
			14236,
			14244,
			14246,
			14250,
			14248,
			14268,
			14273,
			12482,
			12529,
			12723,
			11583,
			11571,
			13191,
			13204,
			13200,
			13211,
			13229,
			13226,
			13234,
			13238,
			9074,
			5808,
			6371,
			5854,
			5844,
			5871,
			5872,
			12086,
			5922,
			10072,
			10071,
			10073,
			10078,
			14528,
			14531,
			14544,
			14548,
			14552,
			14569,
			14570,
			14581,
			14583,
			14587,
			14595,
			14600,
			14604,
			14606,
			11107,
			11243,
			15010,
			11362,
			6021,
			6022,
			6016,
			11962,
			11452,
			11456,
			11649,
			14391,
			14287,
			14410,
			6079,
			6084,
			6094,
			6087,
			6098,
			15165,
			10191,
			6116,
			6122,
			6136,
			6145,
			14511,
			11898,
			13125,
			13123,
			13137,
			13139,
			13143,
			13146,
			10258,
			12784,
			12787,
			12773,
			10731,
			14513,
			14526,
			14523,
			8359,
			15106,
			10374,
			13478,
			13620,
			14061,
			14055,
			6275,
			10727,
			6291,
			6303,
			14366,
			14369,
			14137,
			14142,
			14162,
			14173,
			14167,
			12003,
			11710,
			11914,
			13448,
			13447,
			13453,
			13461,
			11983,
			14379,
			14456,
			14460,
			14773,
			14473,
			12339,
			12355,
			13156,
			13178,
			13171,
			13181,
			6354,
			13105,
			11526,
			6355,
			14725,
			14207,
			14731,
			14267,
			13601,
			14879,
			14656,
			5499,
			14166,
			15286,
			15143,
			15276,
			15255,
			9085,
			13029,
			11546,
			14209,
			15116,
			14858,
			5772,
			15244,
			11376,
			12332,
			14459,
			14817,
			12067,
			13357,
			14769,
			6299,
			14978,
			14591,
			13319,
			15175,
			12524,
			15208,
			15248,
			15125,
			14951,
			12356,
			5595,
			14076,
			13179,
			6146,
			12492,
			5726,
			14325,
			13287,
			5564,
			15249,
			15218,
			14935,
			5742,
			8158,
			9056,
			6074,
			12660,
			12699,
			5846,
			14189,
			12097,
			14401,
			14850,
			14768,
			12747,
			14474,
			5738,
			12497,
			5497,
			5604,
			15237,
			5475,
			15202,
			12712,
			14276,
			12450,
			15045,
			15174,
			15059,
			14107,
			5990,
			15220,
			14497,
			14866,
			15182,
			5810,
			14716,
			12758,
			5549,
			15197,
			15080,
			12002,
			14976,
			12370,
			11131,
			15187,
			14910,
			14841,
			15269,
			12545,
			14995,
			6326,
			15261,
			12846,
			14984,
			14450,
			6216,
			14954,
			15136,
			14818,
			15284,
			13196,
			12814,
			14949,
			14778,
			13244,
			15233,
			15209,
			15128,
			15100,
			14888,
			14678,
			13496,
			14738,
			15164,
			15291,
			14129,
			15034,
			15251,
			14845,
			14303,
			14495,
			13466,
			14997,
			13371,
			6067,
			14846,
			14986,
			12413,
			5890,
			14147,
			6340,
			5519,
			9061,
			14602,
			15133,
			14635,
			5606,
			12986,
			12941,
			11833,
			13097,
			15210,
			13172,
			13325,
			14323,
			14862,
			14358,
			12983,
			15257,
			12605,
			9533,
			14887,
			6107,
			15206,
			13353,
			15173,
			15256,
			12467,
			15200,
			15205,
			14774,
			14992,
			14243,
			14826,
			15119,
			15229,
			15068,
			13019,
			6320,
			14057,
			15108,
			14905,
			14226,
			11626,
			15021,
			14760,
			15199,
			11398,
			6328,
			14791,
			5679,
			15264,
			11381,
			6096,
			14158,
			14990,
			10568,
			14305,
			13201,
			14252,
			5952,
			14157,
			11067,
			15054,
			14408,
			11894,
			15046,
			14873,
			13612,
			13324,
			13512,
			14419,
			15154,
			12687,
			14524,
			14351,
			6101,
			9486,
			15223,
			14123,
			11966,
			14797,
			15050,
			13475,
			12059,
			5972,
			14413,
			13402,
			11734,
			6126,
			14763,
			6004,
			14655,
			15188,
			6069,
			14372,
			12947,
			14815,
			6312,
			14169,
			6071,
			12719,
			12389,
			14631,
			14957,
			13186,
			14165,
			6292,
			6261,
			7850,
			14653,
			7927,
			6142,
			15214,
			14307,
			14256,
			14754,
			14476,
			13548,
			13332,
			6072,
			14467,
			14805,
			13343,
			6115,
			6144,
			14601,
		]);
	}

	private function generateDocument()
	{
		$faker = \Faker\Factory::create();

		$dto = new CreateUpdateDirectiveDto();
		$dto->executed_at = '2024-04-30T14:00:00.000000Z';
		$dto->content = $faker->text(random_int(5, 10000));
		$dto->portfolio = $faker->text(random_int(5, 10000));
		$dto->creator_id = $this->getUsers()->random();
		$dto->author_id = $this->getUsers()->random();
		$dto->executors = $this->getUsers()->random(random_int(1, 10))->values()->toArray();
		$dto->controllers = $this->getUsers()->random(random_int(1, 10))->values()->toArray();
		$dto->observers = $this->getUsers()->random(random_int(1, 10))->values()->toArray();
		$dto->user_id = $this->getUsers()->random();
		$dto->theme_title = $faker->text(100);
		return $this->service->create($dto);
	}
}