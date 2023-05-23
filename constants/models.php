<?php

// Price as of March 2023: https://openai.com/api/pricing/

define( 'MWAI_OPENAI_MODELS', [
  // Base models:
	[ 
		"model" => "gpt-3.5-turbo",
		"name" => "turbo",
		"family" => "turbo",
		"price" => 0.002,
		"type" => "token",
		"unit" => 1 / 1000,
		"maxTokens" => 4096,
		"mode" => "chat",
		"finetune" => false,
		"tags" => ['core', 'chat']
	],
	[ 
		"model" => "gpt-4",
		"name" => "gpt-4",
		"family" => "gpt4",
		"price" => 0.03,
		"priceRules" => "completion_x2",
		"type" => "token",
		"unit" => 1 / 1000,
		"maxTokens" => 8192,
		"mode" => "chat",
		"finetune" => false,
		"tags" => ['core', 'chat']
	],
	[ 
		"model" => "gpt-4-32k",
		"name" => "gpt-4-32k",
		"family" => "gpt4-32k",
		"price" => 0.06,
		"priceRules" => "completion_x2",
		"type" => "token",
		"unit" => 1 / 1000,
		"maxTokens" => 32768,
		"mode" => "chat",
		"finetune" => false,
		"tags" => ['core', 'chat']
	],
  [
		"model" => "text-davinci-003",
		"name" => "davinci-003",
		"family" => "davinci",
		"price" => 0.02,
		"type" => "token",
		"unit" => 1 / 1000,
		"maxTokens" => 2048,
		"mode" => "completion",
		"finetune" => [
			"price" => 0.12
		],
		"tags" => ['core', 'chat', 'finetune']
	],
  [
		"model" => "text-curie-001",
		"name" => "curie-001",
		"family" => "curie",
		"price" => 0.002,
		"type" => "token",
		"unit" => 1 / 1000,
		"maxTokens" => 2048,
		"mode" => "completion",
		"finetune" => [
			"price" => 0.012
		],
		"tags" => ['core', 'chat', 'finetune']
	],
  [
		"model" => "text-babbage-001",
		"name" => "babbage-001",
		"family" => "babbage",
		"price" => 0.0005,
		"type" => "token",
		"unit" => 1 / 1000,
		"maxTokens" => 2048,
		"mode" => "completion",
		"finetune" => [
			"price" => 0.0024
		],
		"tags" => ['core', 'finetune']
	],
  [
		"model" => "text-ada-001",
		"name" => "ada-001",
		"family" => "ada",
		"price" => 0.0004,
		"type" => "token",
		"unit" => 1 / 1000,
		"maxTokens" => 2048,
		"mode" => "completion",
		"finetune" => [
			"price" => 0.0016
		],
		"tags" => ['core', 'finetune']
	],
  // Image models:
  [
		"model" => "dall-e",
		"name" => "dall-e",
		"family" => "dall-e",
		"type" => "image",
		"unit" => 1,
		"options" => [
      [
				"option" => "1024x1024",
				"price" => 0.02
			],
      [
				"option" => "512x512",
				"price" => 0.018
			],
      [
				"option" => "256x256",
				"price" => 0.016
			]
    ],
		"finetune" => false,
		"tags" => ['core', 'image']
  ],
	// Embedding models:
	[
		"model" => "text-embedding-ada-002",
		"name" => "ada-002",
		"family" => "ada",
		"price" => 0.0004,
		"type" => "token",
		"unit" => 1 / 1000,
		"mode" => "embedding",
		"finetune" => false,
		"tags" => ['core', 'embedding'],
	],
	// Audio Models:
	[
		"model" => "whisper-1",
		"name" => "whisper-1",
		"family" => "whisper",
		"price" => 0.00001,
		"type" => "second",
		"unit" => 1,
		"mode" => "speech-to-text",
		"finetune" => false,
		"tags" => ['core', 'audio'],
	]
]);

