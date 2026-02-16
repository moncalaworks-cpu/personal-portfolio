<?php
/**
 * Blog Content Setup Script
 *
 * Creates blog categories and placeholder posts for MonCala AI theme.
 * This script should be run once after theme activation.
 * Run from WordPress CLI: wp eval-file setup-blog-content.php
 * Or directly in admin panel via plugin/admin area.
 *
 * @package MonCala_AI
 */

// Load WordPress
if ( ! function_exists( 'get_bloginfo' ) ) {
	require_once dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/wp-load.php';
}

// Only allow admins
if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( 'Unauthorized' );
}

// Create categories
$categories = array(
	array(
		'slug'        => 'ai-integration',
		'name'        => 'AI Integration',
		'description' => 'Articles about integrating AI and machine learning into existing systems',
	),
	array(
		'slug'        => 'legacy-modernization',
		'name'        => 'Legacy Modernization',
		'description' => 'Strategies and patterns for modernizing legacy code and systems',
	),
	array(
		'slug'        => 'case-studies',
		'name'        => 'Case Studies',
		'description' => 'Real-world case studies and implementation examples',
	),
	array(
		'slug'        => 'tutorials',
		'name'        => 'Tutorials',
		'description' => 'Step-by-step tutorials and how-to guides',
	),
);

$cat_ids = array();
foreach ( $categories as $cat ) {
	$existing = get_term_by( 'slug', $cat['slug'], 'category' );
	if ( ! $existing ) {
		$result = wp_insert_term(
			$cat['name'],
			'category',
			array(
				'slug'        => $cat['slug'],
				'description' => $cat['description'],
			)
		);
		if ( ! is_wp_error( $result ) ) {
			$cat_ids[ $cat['slug'] ] = $result['term_id'];
		}
	} else {
		$cat_ids[ $cat['slug'] ] = $existing->term_id;
	}
}

// Create blog posts
$posts = array(
	array(
		'title'   => 'The 3-Phase Approach to Integrating AI into Legacy Systems',
		'excerpt' => 'Learn the proven 3-phase framework for safely integrating AI capabilities into existing legacy systems without disrupting operations.',
		'content' => 'The integration of AI into legacy systems is one of the most challenging yet rewarding endeavors in modern software development. After working with numerous organizations on this journey, we\'ve identified a repeatable 3-phase approach that minimizes risk while maximizing value.

## Phase 1: Assessment & Proof of Concept

The first phase focuses on understanding your systems and validating that AI integration will provide measurable value.

<pre><code class="language-python">
# Example: Analyzing legacy code patterns
import ast
import os

def analyze_legacy_codebase(root_dir):
    patterns = {}
    for root, dirs, files in os.walk(root_dir):
        for file in files:
            if file.endswith(\'.py\'):
                filepath = os.path.join(root, file)
                with open(filepath, \'r\') as f:
                    tree = ast.parse(f.read())
                    # Analyze patterns
                    patterns[filepath] = len(ast.walk(tree))
    return patterns

# Run analysis
legacy_patterns = analyze_legacy_codebase(\'./legacy_app\')
print(f"Found {len(legacy_patterns)} Python files")
</code></pre>

Key activities:
- System inventory and dependency mapping
- Performance baseline establishment
- AI use case validation
- ROI projection

## Phase 2: Proof of Concept Implementation

Once you\'ve validated that AI integration will work, implement a controlled pilot in a non-critical area.

## Phase 3: Production Rollout

Finally, roll out to production with proper monitoring and fallback mechanisms.

This approach ensures minimal disruption while building organizational confidence in AI systems.',
		'categories' => array( $cat_ids['ai-integration'] ),
	),
	array(
		'title'   => 'Building a RAG System for 15-Year-Old Product Documentation',
		'excerpt' => 'Real-world case study: How we transformed legacy documentation into an AI-powered knowledge base using Retrieval-Augmented Generation (RAG).',
		'content' => 'One of our clients had accumulated 15 years of product documentation across multiple formats: PDFs, Word documents, wikis, and email archives. Searching through this mountain of information was inefficient and error-prone.

## The Challenge

The legacy documentation system had several problems:
- Inconsistent formatting across 15 years
- No centralized index or search
- Required manual knowledge of where information lived
- New employees had steep learning curves

## The Solution: RAG Architecture

We implemented a Retrieval-Augmented Generation (RAG) system that:

1. **Ingests** all documentation into a vector database
2. **Indexes** content for fast semantic search
3. **Retrieves** relevant documents when users ask questions
4. **Generates** contextual answers using an LLM

<pre><code class="language-python">
from pinecone import Pinecone
from openai import OpenAI

class RAGSystem:
    def __init__(self, api_key: str, index_name: str):
        self.client = OpenAI()
        self.pc = Pinecone(api_key=api_key)
        self.index = self.pc.Index(index_name)

    def query(self, question: str) -> str:
        # Get embedding for question
        query_embedding = self.client.embeddings.create(
            input=question,
            model="text-embedding-3-small"
        ).data[0].embedding

        # Search for relevant documents
        results = self.index.query(
            vector=query_embedding,
            top_k=5,
            include_metadata=True
        )

        # Build context from results
        context = "\n".join([
            r["metadata"]["text"] for r in results["matches"]
        ])

        # Generate answer
        response = self.client.chat.completions.create(
            model="gpt-4",
            messages=[
                {"role": "system", "content": "You are a helpful assistant for product documentation."},
                {"role": "user", "content": f"Context:\n{context}\n\nQuestion: {question}"}
            ]
        )

        return response.choices[0].message.content

# Usage
rag = RAGSystem(api_key="sk-...", index_name="docs")
answer = rag.query("How do I configure the payment gateway?")
print(answer)
</code></pre>

## Results

- 95% reduction in time to find information
- 40% reduction in support tickets
- Improved employee onboarding from weeks to days
- Consistent, accurate answers to common questions

## Key Learnings

1. Data quality matters - clean your data before ingestion
2. Chunk documents thoughtfully - balance between context and specificity
3. Monitor LLM hallucinations - implement citation tracking
4. Version your embeddings - reindex when updating the model',
		'categories' => array( $cat_ids['case-studies'] ),
	),
	array(
		'title'   => 'Gradual ML Model Integration: No Downtime, No Risk',
		'excerpt' => 'Deploy ML models to production legacy systems with zero downtime using shadow deployment, A/B testing, and gradual rollout strategies.',
		'content' => 'Deploying machine learning models to production legacy systems is inherently risky. You can\'t just flip a switch. This guide covers proven patterns for gradual, zero-downtime deployments.

## The Problem with Big Bang Deployments

Traditional approaches risk:
- System downtime during cutover
- Cascading failures if the model performs unexpectedly
- No easy rollback path
- Loss of customer trust

## The Solution: Gradual Deployment Pattern

### 1. Shadow Deployment

Run the new model in shadow mode alongside the existing system:

<pre><code class="language-python">
class ShadowDeployment:
    def __init__(self, legacy_model, new_model):
        self.legacy = legacy_model
        self.new = new_model

    def predict(self, data):
        # Always use legacy in production
        result = self.legacy.predict(data)

        # Log new model prediction for comparison
        new_result = self.new.predict(data)
        self.log_prediction_diff(
            expected=result,
            new=new_result,
            data=data
        )

        return result

    def log_prediction_diff(self, expected, new, data):
        # Track divergence for analysis
        diff = abs(expected - new)
        if diff > self.threshold:
            print(f"Divergence detected: {diff}")
</code></pre>

### 2. Canary Deployment

Gradually shift traffic to the new model:

<pre><code class="language-python">
class CanaryDeployment:
    def __init__(self, legacy_model, new_model):
        self.legacy = legacy_model
        self.new = new_model
        self.traffic_shift = 0.0  # 0-1 percentage

    def predict(self, data, request_id: str):
        # Use request ID for consistent routing
        should_use_new = hash(request_id) % 100 < (self.traffic_shift * 100)

        if should_use_new:
            return self.new.predict(data)
        else:
            return self.legacy.predict(data)

    def increase_traffic(self, percentage):
        self.traffic_shift = min(1.0, self.traffic_shift + percentage)
        print(f"Shifted {self.traffic_shift * 100}% traffic to new model")
</code></pre>

### 3. Feature Flags for Quick Rollback

<pre><code class="language-python">
from functools import wraps

class FeatureFlags:
    flags = {}

    @classmethod
    def is_enabled(cls, flag_name: str) -> bool:
        return cls.flags.get(flag_name, False)

    @classmethod
    def disable_flag(cls, flag_name: str):
        cls.flags[flag_name] = False
        # Immediately rollback to legacy

def use_new_model(func):
    @wraps(func)
    def wrapper(*args, **kwargs):
        if FeatureFlags.is_enabled("use_new_ml_model"):
            return func(*args, **kwargs)
        else:
            return legacy_implementation(*args, **kwargs)
    return wrapper

@use_new_model
def predict_customer_churn(customer_id):
    # New model implementation
    pass
</code></pre>

## Deployment Timeline

- **Day 1-3**: Shadow mode, monitor for differences
- **Day 4-7**: Canary with 10% traffic, verify metrics
- **Day 8-10**: Canary with 50% traffic
- **Day 11**: Full cutover with feature flag disabled
- **Day 12-30**: Monitor, keep rollback ready

## Results from Real Deployments

- Zero downtime during any deployment phase
- Improved model performance with 99.9% confidence
- Complete rollback capability at any time
- Customer experience unaffected',
		'categories' => array( $cat_ids['ai-integration'] ),
	),
	array(
		'title'   => 'Database Migration to Vector Store: A Practical Guide',
		'excerpt' => 'Step-by-step guide to migrating from traditional databases to vector stores for AI-powered semantic search and recommendations.',
		'content' => 'Migrating from relational databases to vector stores is becoming increasingly common as organizations build AI-powered features. This guide walks through a real-world migration that preserved data integrity while enabling semantic search.

## Understanding Vector Stores

Vector stores differ from traditional databases in a fundamental way:

<pre><code class="language-sql">
-- Traditional Database: Exact matching
SELECT * FROM products WHERE category = "electronics" AND price < 100;

-- Vector Store: Semantic similarity
SELECT * FROM products
WHERE embedding_distance("wireless headphones", embedding) < 0.5
ORDER BY embedding_distance ASC
LIMIT 10;
</code></pre>

## Migration Architecture

### Step 1: Dual-Write Pattern

During migration, write to both systems:

<pre><code class="language-python">
class DualWriteRepository:
    def __init__(self, sql_db, vector_store):
        self.sql_db = sql_db
        self.vector_store = vector_store

    def save_product(self, product):
        # Write to relational DB
        sql_id = self.sql_db.insert(
            "products",
            product.to_dict()
        )

        # Generate and write embedding
        embedding = self.generate_embedding(product.description)
        self.vector_store.insert(
            id=sql_id,
            vector=embedding,
            metadata=product.to_dict()
        )

        return sql_id

    def generate_embedding(self, text: str):
        # Using OpenAI embeddings API
        from openai import OpenAI
        client = OpenAI()
        response = client.embeddings.create(
            input=text,
            model="text-embedding-3-small"
        )
        return response.data[0].embedding
</code></pre>

### Step 2: Backfill Existing Data

<pre><code class="language-python">
def backfill_vector_store(batch_size=1000):
    products = sql_db.query("SELECT * FROM products")

    for i in range(0, len(products), batch_size):
        batch = products[i:i+batch_size]
        vectors = []

        for product in batch:
            embedding = generate_embedding(product.description)
            vectors.append({
                "id": product.id,
                "vector": embedding,
                "metadata": product.to_dict()
            })

        vector_store.insert_batch(vectors)
        print(f"Backfilled {i + batch_size} products")
</code></pre>

### Step 3: Validation & Comparison

<pre><code class="language-python">
def validate_migration():
    test_queries = [
        "wireless headphones under $100",
        "gaming laptop with RTX 4090",
        "budget smartphone"
    ]

    for query in test_queries:
        sql_results = search_relational_db(query)
        vector_results = search_vector_store(query)

        # Compare results (should be similar, not identical)
        overlap = set(sql_results) & set(vector_results)
        print(f"Query: {query}")
        print(f"Overlap: {len(overlap)}/{len(sql_results)}")
</code></pre>

## Timeline & Rollback

- **Phase 1** (Week 1): Dual-write with SQL as source of truth
- **Phase 2** (Week 2-3): Backfill vector store with all historical data
- **Phase 3** (Week 4): Canary reads from vector store (10%)
- **Phase 4** (Week 5): Gradual traffic shift (10% → 25% → 50% → 100%)
- **Rollback**: Always keep SQL queries ready to switch back to

## Lessons Learned

1. Network latency matters - vector stores may be slower than SQL
2. Embedding consistency - ensure same model for all embeddings
3. Metadata preservation - keep rich metadata in vector store
4. Cost considerations - vector DB charges based on dimensions and queries
5. Version control - track embedding model versions',
		'categories' => array( $cat_ids['legacy-modernization'] ),
	),
	array(
		'title'   => 'LLM Integration Patterns for Legacy PHP Applications',
		'excerpt' => 'Practical patterns for integrating OpenAI and other LLMs into legacy PHP codebases without major refactoring.',
		'content' => 'Integrating large language models into legacy PHP applications doesn\'t require a complete rewrite. Here are proven patterns that work with existing code.

## Pattern 1: API Gateway Wrapper

Wrap LLM API calls in a single class to avoid coupling your legacy code directly to OpenAI:

<pre><code class="language-php">
<?php
class LLMGateway {
    private $client;
    private $model = "gpt-4";

    public function __construct(string $api_key) {
        $this->client = new OpenAI\Client($api_key);
    }

    public function generateResponse(
        string $prompt,
        array $context = [],
        string $system_prompt = ""
    ): string {
        $messages = [];

        if ($system_prompt) {
            $messages[] = [
                "role" => "system",
                "content" => $system_prompt
            ];
        }

        $messages[] = [
            "role" => "user",
            "content" => $this->buildPrompt($prompt, $context)
        ];

        $response = $this->client->chat()->create([
            "model" => $this->model,
            "messages" => $messages,
            "temperature" => 0.7,
            "max_tokens" => 1000
        ]);

        return $response["choices"][0]["message"]["content"];
    }

    private function buildPrompt(string $prompt, array $context): string {
        $contextStr = implode("\n", array_map(
            fn($k, $v) => "$k: $v",
            array_keys($context),
            $context
        ));

        return "Context:\n$contextStr\n\nQuestion: $prompt";
    }
}
</code></pre>

## Pattern 2: Background Job Processing

Don\'t block user requests waiting for LLM responses:

<pre><code class="language-php">
<?php
class LLMBackgroundJob {
    private $queue;
    private $gateway;

    public function enqueueGeneration(
        int $post_id,
        string $prompt
    ): void {
        $job_id = $this->queue->add([
            "type" => "llm_generation",
            "post_id" => $post_id,
            "prompt" => $prompt,
            "created_at" => time()
        ]);

        // Return immediately to user
        update_post_meta($post_id, "_llm_job_id", $job_id);
    }

    public function processJob(array $job): void {
        $response = $this->gateway->generateResponse($job["prompt"]);

        // Store result
        update_post_meta(
            $job["post_id"],
            "_llm_response",
            $response
        );

        // Update post status
        wp_update_post([
            "ID" => $job["post_id"],
            "post_status" => "publish"
        ]);

        // Mark job complete
        $this->queue->complete($job["id"]);
    }
}
</code></pre>

## Pattern 3: Microservice Wrapper

If you can\'t modify legacy PHP directly, wrap it with a Node.js microservice:

<pre><code class="language-php">
<?php
class LLMMicroserviceClient {
    private $endpoint = "http://localhost:3000";

    public function generateText(string $prompt): string {
        $response = wp_remote_post($this->endpoint . "/generate", [
            "body" => json_encode([
                "prompt" => $prompt,
                "model" => "gpt-4"
            ]),
            "headers" => [
                "Content-Type" => "application/json",
                "Authorization" => "Bearer " . getenv("MICROSERVICE_TOKEN")
            ]
        ]);

        $body = json_decode(wp_remote_retrieve_body($response), true);
        return $body["text"] ?? "";
    }
}
</code></pre>

## Pattern 4: Security & Rate Limiting

<pre><code class="language-php">
<?php
class SecureLLMGateway extends LLMGateway {
    private $rate_limiter;

    public function generateResponse(string $prompt, array $context = []): string {
        // Check rate limit
        if (!$this->rate_limiter->allowRequest(get_current_user_id())) {
            throw new Exception("Rate limit exceeded");
        }

        // Sanitize prompt to prevent prompt injection
        $safe_prompt = $this->sanitizePrompt($prompt);

        // Call parent
        return parent::generateResponse($safe_prompt, $context);
    }

    private function sanitizePrompt(string $prompt): string {
        // Remove SQL injection attempts
        $prompt = preg_replace("/DROP|DELETE|UPDATE|INSERT/i", "", $prompt);

        // Remove prompt injection patterns
        $prompt = str_replace(
            ["ignore previous instructions", "you are now"],
            "",
            strtolower($prompt)
        );

        return trim($prompt);
    }
}
</code></pre>

## Integration Checklist

✓ Use API gateway wrapper to decouple from OpenAI
✓ Implement rate limiting by user/IP
✓ Use background jobs for long-running requests
✓ Cache LLM responses when appropriate
✓ Monitor API usage and costs
✓ Sanitize user inputs before sending to LLM
✓ Implement fallback behavior if API is unavailable
✓ Log all LLM interactions for compliance
✓ Version your prompts for reproducibility

## Cost Optimization Tips

- Cache identical prompts and responses
- Use gpt-3.5-turbo for non-critical tasks
- Batch similar requests
- Implement request timeout to avoid wasted credits
- Monitor token usage in real-time',
		'categories' => array( $cat_ids['tutorials'] ),
	),
);

// Create posts
foreach ( $posts as $post_data ) {
	// Check if post already exists
	$existing = get_page_by_title( $post_data['title'], OBJECT, 'post' );
	if ( $existing ) {
		continue; // Skip if already exists
	}

	// Create post
	$post_id = wp_insert_post( array(
		'post_type'    => 'post',
		'post_title'   => $post_data['title'],
		'post_content' => $post_data['content'],
		'post_excerpt' => $post_data['excerpt'],
		'post_status'  => 'publish',
		'post_author'  => get_current_user_id(),
	) );

	if ( ! is_wp_error( $post_id ) ) {
		// Assign categories
		wp_set_post_categories( $post_id, $post_data['categories'] );

		// Set featured image (use WordPress logo as placeholder)
		if ( has_post_thumbnail( 1 ) ) { // Post ID 1 should have an image
			$thumbnail_id = get_post_thumbnail_id( 1 );
			set_post_thumbnail( $post_id, $thumbnail_id );
		}

		echo "✓ Created post: " . esc_html( $post_data['title'] ) . "\n";
	} else {
		echo "✗ Failed to create post: " . esc_html( $post_data['title'] ) . "\n";
	}
}

echo "✓ Blog content setup complete!\n";
