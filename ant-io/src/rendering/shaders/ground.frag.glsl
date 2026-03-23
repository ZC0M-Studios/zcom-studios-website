#version 300 es
// =============================================================================
// ANCHOR:SHADER_GROUND_FRAG
// Description: Procedural dirt ground using value noise.
//              Blends two dirt colors with vertical gradient + noise texture.
// Uniforms:
//   u_color_a    - top-left base dirt color
//   u_color_b    - bottom-right dirt color
//   u_noise_scale - noise frequency (higher = more granular)
// =============================================================================

precision mediump float;

in vec2 v_uv;

uniform vec3 u_color_a;      // e.g. (0.22, 0.16, 0.10)
uniform vec3 u_color_b;      // e.g. (0.30, 0.22, 0.14)
uniform float u_noise_scale; // e.g. 18.0

out vec4 fragColor;

// Simple value noise helper
float hash2(vec2 p) {
    p = fract(p * vec2(127.1, 311.7));
    p += dot(p, p + 19.31);
    return fract(p.x * p.y);
}

float valueNoise(vec2 p) {
    vec2 i = floor(p);
    vec2 f = fract(p);
    vec2 u = f * f * (3.0 - 2.0 * f); // smoothstep

    float a = hash2(i);
    float b = hash2(i + vec2(1.0, 0.0));
    float c = hash2(i + vec2(0.0, 1.0));
    float d = hash2(i + vec2(1.0, 1.0));

    return mix(mix(a, b, u.x), mix(c, d, u.x), u.y);
}

void main() {
    float n1 = valueNoise(v_uv * u_noise_scale);
    float n2 = valueNoise(v_uv * u_noise_scale * 3.7 + vec2(5.3, 1.7)) * 0.4;
    float noise = (n1 + n2) / 1.4;

    vec3 base = mix(u_color_a, u_color_b, v_uv.y + (v_uv.x - 0.5) * 0.15);
    vec3 col  = base + vec3(noise * 0.06 - 0.03);

    fragColor = vec4(clamp(col, 0.0, 1.0), 1.0);
}
